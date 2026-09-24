<?php

namespace App\Services\Gemini;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiVisionService
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
     * Send one or more face images to Gemini and get back a structured
     * facial-analysis payload.
     *
     * @param  array<string, string>  $images  e.g. ['front' => absolute_path, 'side' => absolute_path]
     * @return array<string, mixed>
     */
    public function analyzeFace(array $images): array
    {
        $parts = [['text' => $this->prompt()]];

        foreach ($images as $label => $path) {
            $parts[] = ['text' => "The following image is the \"{$label}\" view."];
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $this->mimeType($path),
                    'data'      => base64_encode(file_get_contents($path)),
                ],
            ];
        }

        $response = Http::timeout(60)
            ->withHeaders(['x-goog-api-key' => $this->apiKey])
            ->post("{$this->baseUrl}/models/{$this->model}:generateContent", [
                'contents' => [
                    ['role' => 'user', 'parts' => $parts],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema'   => $this->schema(),
                    'temperature'      => 0.4,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini request failed: '.$response->body());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $text) {
            throw new RuntimeException('Gemini returned an empty analysis.');
        }

        return json_decode($text, true, flags: JSON_THROW_ON_ERROR);
    }

    protected function mimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    protected function prompt(): string
    {
        return <<<'PROMPT'
            You are a facial-proportion analysis engine. Examine the supplied
            face image(s) and rate visible facial proportions on a 0.0-10.0
            scale for each metric below. Base every score strictly on
            geometric proportion and symmetry, never on identity, race,
            gender, or any protected characteristic. Return ONLY data that
            matches the provided JSON schema, no extra commentary.
            PROMPT;
    }

    /** @return array<string, mixed> */
    protected function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'harmony_score'   => ['type' => 'number'],
                'symmetry'        => ['type' => 'number'],
                'jawline'         => ['type' => 'number'],
                'cheekbones'      => ['type' => 'number'],
                'nose'            => ['type' => 'number'],
                'skin_quality'    => ['type' => 'number'],
                'facial_width_height_ratio' => ['type' => 'number'],
                'key_strengths'   => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'label' => ['type' => 'string'],
                            'score' => ['type' => 'number'],
                        ],
                        'required' => ['label', 'score'],
                    ],
                ],
                'regions' => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name'  => ['type' => 'string'],
                            'score' => ['type' => 'number'],
                            'note'  => ['type' => 'string'],
                        ],
                        'required' => ['name', 'score'],
                    ],
                ],
                'summary' => ['type' => 'string'],
            ],
            'required' => [
                'harmony_score', 'symmetry', 'jawline', 'cheekbones',
                'nose', 'skin_quality', 'key_strengths', 'summary',
            ],
        ];
    }
}
