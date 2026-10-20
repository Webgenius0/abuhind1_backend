<?php

namespace Database\Seeders;

use App\Models\VideoTutorial;
use Illuminate\Database\Seeder;

class VideoTutorialSeeder extends Seeder
{
    public function run(): void
    {
        $tutorials = [
            [
                'title'        => 'Getting Started: How to Take Your First Face Scan',
                'description'  => 'A quick walkthrough of uploading a front and side photo, and what to expect from your results.',
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status'       => 'active',
            ],
            [
                'title'        => 'Understanding Your Harmony Score',
                'description'  => 'We break down what the overall score means and how each trait contributes to it.',
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status'       => 'active',
            ],
            [
                'title'        => 'How to Generate and Share Your Score Card',
                'description'  => 'Learn how to generate a shareable card and download it as an image or PDF.',
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status'       => 'active',
            ],
            [
                'title'        => 'Premium Features Overview',
                'description'  => 'A look at what unlocks with Premium, including detailed front and side analysis.',
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status'       => 'active',
            ],
            [
                'title'        => 'Premium Features Overview 2',
                'description'  => 'A look at what unlocks with Premium, including detailed front and side analysis.',
                'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'status'       => 'active',
            ],
        ];

        foreach ($tutorials as $tutorial) {
            VideoTutorial::create($tutorial);
        }
    }
}
