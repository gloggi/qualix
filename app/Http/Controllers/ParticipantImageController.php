<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Participant;
use Illuminate\Support\Facades\Storage;

class ParticipantImageController extends Controller
{
    public function show(Course $course, Participant $participant)
    {
        if (!$participant->image_url || !Storage::exists($participant->image_url)) {
            abort(404);
        }

        $mimeType = Storage::mimeType($participant->image_url) ?? 'application/octet-stream';
        $contents = Storage::get($participant->image_url);

        return response($contents, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'private, max-age=3600')
            ->header('Content-Disposition', 'inline')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
