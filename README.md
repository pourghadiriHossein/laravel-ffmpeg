# آموزش نصب و کار با FFMpeg در Laravel 11

## در صورت نیاز به مطالعه مستند FFMpeg می توانید بر روی <a href="https://github.com/protonemedia/laravel-ffmpeg">لینک</a> کلیک کنید.

### برای شروع ابتدا یک پروژه Laravel بسازید.
```bash
composer create-project laravel/laravel Stream
```

### وارد پروژه شوید، مرحله بعد نصب FFMpeg بر روی پروژه است
```bash
composer require pbmedia/laravel-ffmpeg
```
### بعد نصب لازم است تا تنظیمات زیر را انجام دهید

###### اول وارد پوشه bootstrap شوید و سپس در داخل فایل providers.php در بخش return مقدار زیر را وارد کنید
```bash
ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider::class,
```

###### دوم برای وارد کردن alias یک Provider می سازیم
```bash
php artisan make:provider AliasServiceProvider
```

###### سپس در بخش register فایل AliasServiceProvider مقدار زیر را وارد می کنیم.
```bash
$loader = AliasLoader::getInstance();

$loader->alias('FFMpeg', \ProtoneMedia\LaravelFFMpeg\Support\FFMpeg::class);
```
###### برای موارد فوق باید AliasLoader را use کنید
```bash
use Illuminate\Foundation\AliasLoader;
```

###### سپس بعد این مرحله وارد  <a href="https://github.com/BtbN/FFmpeg-Builds/releases">لینک</a> شده و نسخه مناسب سیستم عامل خود را دانلود کنید.

###### آن را در مسیر اصلی پروژه خود استخراج کنید.

###### سپس در .env دو متغیر زیر را تعریف کنید

```bash
FFMPEG_BINARIES="./FFMpeg/bin/ffmpeg.exe"
FFPROBE_BINARIES="./FFMpeg/bin/ffprobe.exe"
```

###### سپس دستور زیر را جهت ساخت فایل config آن بزنید
```bash
php artisan vendor:publish --provider="ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider"
```

###### بعد این مرحله اقدام به ساخت یک console command کنید
```bash
php artisan make:command VideoEncode
```
###### در بخش handle فایل VideoEncode قطعه کد زیر را بنویسید
```bash
$lowBitRate = (new X264)->setKiloBitrate(1000);
$midBitRate = (new X264)->setKiloBitrate(2500);
$highBitRate = (new X264)->setKiloBitrate(5000);

$this->info('Converting Video Start:');

FFMpeg::fromDisk('uploads')
    ->open('video.mp4')
    ->exportForHLS()
    ->addFormat($lowBitRate)
    ->addFormat($midBitRate)
    ->addFormat($highBitRate)
    ->onProgress(function ($progress) {
        $this->info("Progress: {$progress}%");
    })
    ->toDisk('privates')
    ->save('video.m3u8');
```
###### برای موارد فوق لازم است تا گزینه های زیر را use کنید
```bash
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
```
###### سپس بعد این مرحله لازم است disk های استفاده شده خود را تعریف کنیم. به همین منظور در پوشه config در فایل filesystems مقادیر زیر را در disks اضافه می کنیم
```bash
'uploads' => [
    'driver' => 'local',
    'root' => public_path('uploads'),
    'url' => env('APP_URL').'/public/uploads',
    'visibility' => 'public',
],

'privates' => [
    'driver' => 'local',
    'root' => public_path('privates'),
    'url' => env('APP_URL').'/public/privates',
    'visibility' => 'public',
],
```
###### حال نوبت آن است که در پوشه public دو پوشه uploads و privates را بسازید و یک فایل ویدئویی با فرمت mp4 در آن قرار دهید و نام آن را video بگذارید

###### حال نوبت آن است تا دستور دهید عمل تبدیل انجام شد، دستور زیر را بزنید
```bash
php artisan app:video-encode
```
###### پس از پایان تبدیل شد، به فایل welcome.blade.php رفته و مد زیر را در آن جای گذاری کنید
```bash
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FFMpeg Laravel</title>
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://unpkg.com/video.js/dist/video-js.css">
</head>
<body>
    <video-js id="my_video_1" class="vjs-default-skin vjs-big-play-centered" controls preload="auto" data-setup="{'fluid':true}">
        <source src="{{asset('privates/video.m3u8')}}" type="application/x-mpegURL">
    </video-js>

    <script src="https://unpkg.com/video.js/dist/video.js"></script>
    <script src="https://unpkg.com/@videojs/http-streaming/dist/videojs-http-streaming.js"></script>

    <script>
        var player = videojs('my_video_1')
    </script>
</body>
</html>
```



