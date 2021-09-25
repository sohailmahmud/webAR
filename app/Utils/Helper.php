<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class Helper
{

    /**
     * Get e set app configurations in configs.json file
     *
     * @param  string  $key
     * @param  string|integer  $value
     */
    public static function config($key = null, $value = null)
    {
        if (!Storage::disk('local')->exists("configs/$key.json")) {
            Storage::disk('local')->put("configs/$key.json", '{}');
        }
        $config = json_decode(Storage::disk('local')->get("configs/$key.json"), true);

        if (!is_null($key) && is_null($value)) {
            if (isset($config[$key])) {
                return $config[$key];
            }
            return null;
        }

        if (!is_null($key) && !is_null($value)) {
            $config[$key] = $value;
            $config_json = json_encode($config);
            Storage::disk('local')->put("configs/$key.json", $config_json);
        }
    }



    /**
     * Turns the image into a thumbnail
     *
     * @param  string  $filepath
     * @param  integer  $size
     * @param  integer  $quality 0 - 100
     * @return void
     */
    public static function thumbnail($filepath, $size = 100, $quality = 90)
    {
        $img = Image::make($filepath);
        $img_width = $img->width();
        $img_height = $img->height();
        if ($img_width < $img_height) {
            $w = $size;
            $h = null;
        } else {
            $w = null;
            $h = $size;
        }
        $img->resize($w, $h, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->crop($size, $size);
        $img->save($filepath, $quality);
    }



    /**
     * Resizes the image to a maximum width or height
     *
     * @param  string  $filepath
     * @param  integer  $maxwidth
     * @param  integer  $maxheight
     * @param  integer  $quality 0 - 100
     * @return void
     */
    public static function resizeImage($filepath, $maxwidth = null, $maxheight = null, $quality = 90)
    {
        $img = Image::make($filepath);
        $w = $maxwidth;
        $h = $maxheight;
        $img->resize($w, $h, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($filepath, $quality);
    }



    /**
     * Generates short URL.
     *
     * @param String $longURL
     * @return String Bit.ly or custom short URL .
     */
    public static function getShortURL($longURL)
    {
        $bitlyAccessToken = self::configs('BITLY_ACCESS_TOKEN');
        if ($bitlyAccessToken) {
            // https://artisansweb.net/create-tinyurl-using-bitly-api-php/
            $url = 'https://api-ssl.bitly.com/v4/bitlinks';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['long_url' => $longURL]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $bitlyAccessToken",
                "Content-Type: application/json"
            ]);
            
            $arr_result = json_decode(curl_exec($ch));
            $short_url = $arr_result->link?? $longURL;
            $short_url = str_replace(['http://', 'https://'], '', $short_url);
            return $short_url;
        }

        return $longURL;
    }



    /**
     * Deletes ao files and folders.
     *
     * @param String $dir
     * @return void
     */
    public static function clearDirectory($dir)
    {
        $structure = glob(rtrim($dir, '/') . '/*');
        if (is_array($structure)) {
            foreach ($structure as $file) {
                if (is_dir($file)) {
                    self::clearDirectory($file);
                } elseif (is_file($file)) {
                    unlink($file);
                }
            }
        }
        rmdir($dir);
    }
    


    /**
     * Optimizes video size.
     * https://github.com/PHP-FFMpeg/PHP-FFMpeg
     *
     * @param String $path
     * @return void
     */
    public static function optimizeVideo($path)
    {
        $pathArr = explode('/', $path);
        $filename = $pathArr[count($pathArr) - 1];
        $tempPath = str_replace($filename, "temp/$filename", $path);

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => env('FFMPEG_BINARIES'),   // 'C:/ffmpeg-4.3.1-win64-static/bin/ffmpeg.exe' | /opt/local/ffmpeg/bin/ffmpeg
            'ffprobe.binaries' => env('FFPROB_BINARIES'),   // 'C:/ffmpeg-4.3.1-win64-static/bin/ffprobe.exe' | /opt/local/ffmpeg/bin/ffprobe
            'timeout'          => 7200,                     // The timeout for the underlying process
            'ffmpeg.threads'   => 12,                       // The number of threads that FFMpeg should use
        ]);
        
        $video = $ffmpeg->open($path);
        
        // Save a frame (poster)
        $posterName = "poster_".Str::random(5).".jpg";
        $posterPath = str_replace($filename, $posterName, $path);
        $video
            ->frame(TimeCode::fromSeconds(5))
            ->save($posterPath);

        // To solve: Unknown encoder 'libfaac' pass aac or libmp3lame in constructor
        // https://stackoverflow.com/questions/19774975/unknown-encoder-libfaac
        // https://github.com/PHP-FFMpeg/PHP-FFMpeg/issues/295
        
        $format = new X264('aac');
        // $format->on('progress', function ($video, $format, $percentage) {
        //     //logger($percentage);
        // });
        
        $format
            ->setKiloBitrate(1000)
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(256);
        
        $video->save($format, $tempPath);

        Storage::delete("assets/$filename");
        Storage::move("assets/temp/$filename", "assets/$filename");
        
        return [
            'optimized_size' => Storage::size("assets/$filename") / (1024*2014),
            'poster' => $posterName
        ];
        
    }

}
