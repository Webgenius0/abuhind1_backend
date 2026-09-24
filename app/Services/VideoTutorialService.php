<?php

namespace App\Services;

use App\Repositories\VideoTutorial\VideoTutorialRepositoryInterface;

class VideoTutorialService
{
    protected $videoRepository;

    public function __construct(VideoTutorialRepositoryInterface $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function getAllVideos()
    {
        return $this->videoRepository->getAll();
    }

    public function storeVideo(array $data)
    {
        // Ekhane YouTube link validation ba formatting lagle korte paren
        return $this->videoRepository->create($data);
    }
}
