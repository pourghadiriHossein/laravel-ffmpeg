<?php

namespace App\Console\Commands;

use FFMpeg\Format\Video\X264;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
class VideoEncode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:video-encode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowBitRate = (new X264)->setKiloBitrate(1000);
        // $midBitRate = (new X264)->setKiloBitrate(2500);
        // $highBitRate = (new X264)->setKiloBitrate(5000);

        $this->info('Converting Video Start:');

        FFMpeg::fromDisk('uploads')
            ->open('video.mp4')
            ->exportForHLS()
            ->addFormat($lowBitRate)
            ->withRotatingEncryptionKey(function($filename, $contents){
                Storage::disk('secrets')->put($filename, $contents);
            })
            // ->addFormat($midBitRate)
            // ->addFormat($highBitRate)
            ->onProgress(function ($progress) {
                $this->info("Progress: {$progress}%");
            })
            ->toDisk('privates')
            ->save('video.m3u8');

    }
}
