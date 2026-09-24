<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoTutorial;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class VideoTutorialController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $tutorials = VideoTutorial::where('status', 'active')->latest()->get();

        return $this->success('Video tutorials fetched successfully', $tutorials);
    }
}
