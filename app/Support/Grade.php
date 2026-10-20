<?php

namespace App\Support;

class Grade
{
    public static function from(float $score): array
    {
        return match (true) {
            $score >= 8.5 => ['key' => 'excellent', 'label' => 'ممتاز'],
            $score >= 7.0 => ['key' => 'good',      'label' => 'جيد'],
            $score >= 5.0 => ['key' => 'average',   'label' => 'متوسط'],
            default       => ['key' => 'poor',      'label' => 'ضعيف'],
        };
    }
}
