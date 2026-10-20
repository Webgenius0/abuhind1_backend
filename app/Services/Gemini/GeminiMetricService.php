<?php

namespace App\Services\Gemini;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiMetricService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = (string) config('services.gemini.key');
        $this->model   = (string) config('services.gemini.model');
        $this->baseUrl = (string) config('services.gemini.base');
    }

    /**
     * @param  array<string, string>  $images  ['front' => path, 'side' => path (optional)]
     * @return array<string, float>  metric key => measured value
     */
    public function measure(array $images): array
    {
        $hasSide = isset($images['side']);

        $metrics = collect(config('face_metrics', []))
            ->filter(fn ($m) => $hasSide || ($m['view'] ?? 'front') === 'front');

        if ($metrics->isEmpty()) {
            return [];
        }

        $list = $metrics
            ->map(fn ($m, $key) => "- {$key} [".($m['view'] ?? 'front')."]: {$m['title']}"
                .(! empty($m['unit']) ? " (unit: {$m['unit']})" : '')
                .(! empty($m['definition']) ? " — how to measure: {$m['definition']}" : ''))
            ->implode("\n");

        $prompt = "You are a facial-measurement engine. For every metric listed below, "
            ."estimate its measured value from the supplied image(s), in the stated unit "
            ."(this is a raw measurement, NOT a 0-10 score). Metrics marked [front] are "
            ."measured from the front image, [side] from the side image. Return exactly one "
            ."entry per listed key, using the key exactly as written. Base measurements only "
            ."on visible geometry.\n\n{$list}\n\nReturn ONLY JSON matching the schema.";

        $parts = [['text' => $prompt]];

        foreach ($images as $label => $path) {
            $parts[] = ['text' => "The following image is the \"{$label}\" view."];
            $parts[] = ['inline_data' => [
                'mime_type' => $this->mimeType($path),
                'data'      => base64_encode(file_get_contents($path)),
            ]];
        }

        $response = Http::timeout(120)
            ->withHeaders(['x-goog-api-key' => $this->apiKey])
            ->post("{$this->baseUrl}/models/{$this->model}:generateContent", [
                'contents' => [['role' => 'user', 'parts' => $parts]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema'   => [
                        'type' => 'object',
                        'properties' => [
                            'metrics' => [
                                'type'  => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'key'   => ['type' => 'string', 'format' => 'enum', 'enum' => $metrics->keys()->all()],
                                        'value' => ['type' => 'number'],
                                    ],
                                    'required' => ['key', 'value'],
                                ],
                            ],
                        ],
                        'required' => ['metrics'],
                    ],
                    'temperature'     => 0.2,
                    'maxOutputTokens' => 4096,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini metric request failed: '.$response->body());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $text) {
            throw new RuntimeException('Gemini returned empty metrics.');
        }

        $decoded = json_decode($text, true, flags: JSON_THROW_ON_ERROR);

        return collect($decoded['metrics'] ?? [])
            ->filter(fn ($r) => isset($r['key'], $r['value']) && $metrics->has($r['key']))
            ->pluck('value', 'key')
            ->all();
    }

    protected function mimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }
}
