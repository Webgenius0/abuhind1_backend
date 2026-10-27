<?php

namespace App\Services\Gemini;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * AI-driven facial symmetry and proportion analysis engine via Gemini Vision.
 */
class GeminiVisionService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    /**
     * Initialize Gemini API credentials and configuration.
     */
    public function __construct()
    {
        $this->apiKey  = (string) config('services.gemini.key');
        $this->model   = (string) config('services.gemini.model');
        $this->baseUrl = (string) config('services.gemini.base');
    }

    /**
     * Analyze facial proportions and geometry from uploaded images.
     *
     * @param  array<string, string>  $images    e.g. ['front' => path, 'side' => path]
     * @param  bool                   $detailed  Include granular regional breakdown
     * @return array<string, mixed>
     */
    public function analyzeFace(array $images, bool $detailed = false): array
    {
        // 1. Prepare multimodal prompt and base64 encoded image parts
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

        // 2. Request structured facial analysis with deterministic temperature (0.0)
        $response = Http::timeout(60)
            ->withHeaders(['x-goog-api-key' => $this->apiKey])
            ->post("{$this->baseUrl}/models/{$this->model}:generateContent", [
                'contents' => [
                    ['role' => 'user', 'parts' => $parts],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema'   => $this->schema($detailed, isset($images['side'])),
                    'temperature'      => 0.0,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini request failed: ' . $response->body());
        }

        // 3. Extract and parse structured JSON response
        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $text) {
            throw new RuntimeException('Gemini returned an empty analysis.');
        }

        return json_decode($text, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Detect image MIME type based on file extension.
     */
    protected function mimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    /**
     * Build the structured analysis prompt with face validation and calibrated scoring rubric.
     */
    protected function prompt(bool $detailed): string
    {
        $base = <<<'PROMPT'
            You are a rigorous, scientific facial-proportion analysis engine calibrated to the standards of professional aesthetic analysis platforms (such as FaceIQ Labs).

            STEP 1: MANDATORY FACE VALIDATION
            - You MUST inspect each supplied image before performing any analysis.
            - The "front" image MUST contain a clearly visible, authentic front-facing human face with recognizable facial features (eyes, nose, mouth).
            - The "side" image (if provided) MUST contain a clearly visible, authentic side-profile of a human face showing the profile contours (forehead, nose, lips, jawline, chin).
            - If ANY supplied image is completely black, blank, severely dark, blurred, obstructed, non-human, or does NOT contain a clear face:
              * Set "face_detected" to false.
              * Set "front_face_detected" to false if the front view is invalid, or true if valid.
              * Set "side_face_detected" to false if the side view is invalid, or true if valid.
              * Set "validation_error" to a short, premium description in English (e.g. "Face not detected in side view. Please upload a clear photo." or "Face not detected in front view. Please upload a clear photo.").
              * For required numeric scores, return 0.0. For "key_strengths", return an empty array. For "summary", explain in Arabic that the face could not be analyzed due to an invalid image.

            STEP 2: STRICT OBJECTIVE SCORING CRITERIA (NO GRADE INFLATION)
            - If valid faces are present, evaluate visible facial proportions on a strict 0.0 - 10.0 scale following an authentic population bell curve:
              * 0.0 - 3.9: Severe structural asymmetry, recessed jaw/chin, pronounced disproportions.
              * 4.0 - 4.9: Below average; noticeable asymmetry, weak bone definition, soft or recessed contours.
              * 5.0 - 5.9: Average human baseline (median population score). Normal everyday symmetry and standard facial proportions.
              * 6.0 - 6.9: Above average; clear definition of jawline and cheekbones, good facial balance and harmony.
              * 7.0 - 7.9: Distinctly attractive / aesthetic; sharp, well-defined bone structure, high harmony, very good symmetry. (CRITICAL: Never give 7.0+ to ordinary, plain, or flawed faces).
              * 8.0 - 8.9: Model-tier / exceptional; rare bone structure, near-ideal facial thirds, high angularity and dimorphism.
              * 9.0 - 10.0: Flawless ideal aesthetic proportions (extremely rare).
            - DO NOT inflate scores out of politeness or courtesy. Base every score strictly on geometric proportion and symmetry, never on identity, race, gender, or any protected characteristic.
            - "angularity" measures how sharp/defined vs. soft/rounded the bone structure is.
            - "dimorphism" measures how strongly the face expresses typical sex-linked structural traits, purely as a proportion metric.
            PROMPT;

        if ($detailed) {
            $base .= "\n\n" . <<<'PROMPT'
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
            . "regions names and notes) in Arabic as used in Saudi Arabia. Keep all JSON keys, "
            . "metric keys and enum values exactly as specified, in English. Use Western digits (0-9).";

        $base .= "\n\nReturn ONLY data that matches the provided JSON schema, no extra commentary.";

        return $base;
    }

    /**
     * Define the strict JSON schema for structured Gemini analysis output.
     *
     * @return array<string, mixed>
     */
    protected function schema(bool $detailed, bool $hasSideImage): array
    {
        $properties = [
            'face_detected'             => ['type' => 'boolean'],
            'front_face_detected'       => ['type' => 'boolean'],
            'validation_error'          => ['type' => 'string'],
            'harmony_score'             => ['type' => 'number'],
            'symmetry'                  => ['type' => 'number'],
            'jawline'                   => ['type' => 'number'],
            'cheekbones'                => ['type' => 'number'],
            'nose'                      => ['type' => 'number'],
            'skin_quality'              => ['type' => 'number'],
            'angularity'                => ['type' => 'number'],
            'dimorphism'                => ['type' => 'number'],
            'facial_width_height_ratio' => ['type' => 'number'],
            'key_strengths'             => [
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
            'face_detected',
            'front_face_detected',
            'harmony_score',
            'symmetry',
            'jawline',
            'cheekbones',
            'nose',
            'skin_quality',
            'angularity',
            'dimorphism',
            'key_strengths',
            'summary',
        ];

        if ($hasSideImage) {
            $properties['side_face_detected'] = ['type' => 'boolean'];
            $required[] = 'side_face_detected';
        }

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
