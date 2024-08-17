<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoController extends Controller
{
    public function playlist($playlist)
    {
        return FFMpeg::dynamicHLSPlaylist()
            ->fromDisk('privates')
            ->open("{$playlist}")
            ->setKeyUrlResolver(function ($key) {
                return route('video.key', ['key' => $key]);
            })
            ->setPlaylistUrlResolver(function ($playlist) {
                return route('video.playlist', ['playlist' => $playlist]);
            })
            ->setMediaUrlResolver(function ($media) {
                $test = Storage::disk('privates')->url("{$media}");
                return str_replace('public/', '', $test);
            });
    }

    public function key($key)
    {
        return Storage::disk('secrets')->download($key);
    }
}
