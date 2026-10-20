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
     * @param  bool  $detailed  whether to also request the granular per-region
     *                          breakdown (premium users only — costs more tokens)
     * @return array<string, mixed>
     */
    public function analyzeFace(array $images, bool $detailed = false): array
    {
        $parts = [['text' => $this->prompt($detailed)]];

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
                    'responseSchema'   => $this->schema($detailed, isset($images['side'])),
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

    protected function prompt(bool $detailed): string
    {
        $base = <<<'PROMPT'
            You are a facial-proportion analysis engine. Examine the supplied
            face image(s) and rate visible facial proportions on a 0.0-10.0
            scale for each metric below. Base every score strictly on
            geometric proportion and symmetry, never on identity, race,
            gender, or any protected characteristic. "angularity" measures
            how sharp/defined vs. soft/rounded the bone structure is.
            "dimorphism" measures how strongly the face expresses typical
            sex-linked structural traits, purely as a proportion metric.
            PROMPT;

        if ($detailed) {
            $base .= "\n\n".<<<'PROMPT'
                Additionally provide a granular breakdown. For the front-view
                image, populate "front_regions" with ~20-25 entries covering
                specific sub-metrics grouped under the zones "Upper Third",
                "Middle Third", and "Lower Third" (e.g. forehead height ratio,
                brow ridge prominence, interocular ratio, canthal tilt,
                cheekbone width, nose width/length, philtrum length, jaw
                width, gonial angle, chin height/width, lip fullness, mouth
                width). If a side-view image is also supplied, populate
                "side_regions" with ~9-10 profile-only metrics (e.g. facial
                convexity, nasofrontal angle, nasolabial angle, chin
                projection, forehead slope, side gonial angle, submental
                angle, brow ridge projection). If no side image is supplied,
                return an empty array for "side_regions".
                PROMPT;
        }
        $base .= "\n\nLanguage: write every free-text string value (summary, key_strengths labels, "
            ."regions names and notes) in Arabic as used in Saudi Arabia. Keep all JSON keys, "
            ."metric keys and enum values exactly as specified, in English. Use Western digits (0-9).";

        $base .= "\n\nReturn ONLY data that matches the provided JSON schema, no extra commentary.";

        return $base;
    }

    /** @return array<string, mixed> */
    protected function schema(bool $detailed, bool $hasSideImage): array
    {
        $properties = [
            'harmony_score'   => ['type' => 'number'],
            'symmetry'        => ['type' => 'number'],
            'jawline'         => ['type' => 'number'],
            'cheekbones'      => ['type' => 'number'],
            'nose'            => ['type' => 'number'],
            'skin_quality'    => ['type' => 'number'],
            'angularity'      => ['type' => 'number'],
            'dimorphism'      => ['type' => 'number'],
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
        ];

        $required = [
            'harmony_score', 'symmetry', 'jawline', 'cheekbones',
            'nose', 'skin_quality', 'angularity', 'dimorphism',
            'key_strengths', 'summary',
        ];

        if ($detailed) {
            $detailedRegionItem = [
                'type' => 'object',
                'properties' => [
                    'zone'   => ['type' => 'string'],
                    'metric' => ['type' => 'string'],
                    'score'  => ['type' => 'number'],
                    'note'   => ['type' => 'string'],
                ],
                'required' => ['zone', 'metric', 'score'],
            ];

            $properties['front_regions'] = ['type' => 'array', 'items' => $detailedRegionItem];
            $required[] = 'front_regions';

            if ($hasSideImage) {
                $properties['side_regions'] = ['type' => 'array', 'items' => $detailedRegionItem];
                $required[] = 'side_regions';
            }
        }

        return [
            'type'       => 'object',
            'properties' => $properties,
            'required'   => $required,
        ];
    }
}
