<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Utils\Helper;

class AddEntityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $max_filesize = config('app.APP_UPLOAD_MAX_FILESIZE');
        $max_width = config('app.APP_UPLOAD_MAX_MEDIA_WIDTH');
        $max_height = config('app.APP_UPLOAD_MAX_MEDIA_HEIGHT');
        $max_duration = config('app.APP_UPLOAD_MAX_MEDIA_DURATION');
        

        $type = $this->input('type'); // a-image, a-sound, a-video, a-gltf-model
        switch ($type) {
                      
            case 'a-image':
                $validation = [
                    'name' => 'required|string|max:50',
                    'type' => 'required|in:a-image,a-sound,a-video,a-text,a-gltf-model',
                    'marker_id' => 'required|exists:markers,id',
                    'props_asset_type' => 'required|in:model,img,video,audio',
                    'props_asset_width' => 'required|numeric|max:' . $max_width,                                    // width (px)
                    'props_asset_height' => 'required|numeric|max:' . $max_height,                                  // height (px)
                    'props_asset_size' => 'required|numeric|max:' . $max_filesize*1024,                             // bytes
                    'props_asset_file' => 'required|file|mimes:jpg,jpeg,png,gif|max:' . $max_filesize,                  // jpg, png, gif, kilobytes
                ];
                break;
            case 'a-video':
                $validation = [
                    'name' => 'required|string|max:50',
                    'type' => 'required|in:a-image,a-sound,a-video,a-text,a-gltf-model',
                    'marker_id' => 'required|exists:markers,id',
                    'props_asset_type' => 'required|in:model,img,video,audio',
                    'props_asset_width' => 'required|numeric|max:' . $max_width,                                    // width (px)
                    'props_asset_height' => 'required|numeric|max:' . $max_height,                                  // height (px)
                    'props_asset_size' => 'required|numeric|max:' . $max_filesize*1024,                             // bytes
                    'props_asset_duration' => 'required|numeric|max:' . $max_duration,                              // duration (double) seconds
                    'props_asset_loop' => 'present|in:true,false',                                                  // loop? (true|false)
                    'props_asset_preload' => 'present|in:none,auto',                                                // preload? (none|auto)
                    'props_asset_autoplay' => 'present|in:true,false',                                              // autoplay? (true|false)
                    'props_asset_file' => 'required|file|mimes:mp4|max:' . $max_filesize,                           // mp4, kilobytes
                ];
                break; 
            case 'a-sound':
                $validation = [
                    'name' => 'required|string|max:50',
                    'type' => 'required|in:a-image,a-sound,a-video,a-text,a-gltf-model',
                    'marker_id' => 'required|exists:markers,id',
                    'props_asset_type' => 'required|in:model,img,video,audio',
                    'props_asset_size' => 'required|numeric|max:' . $max_filesize*1024,                             // bytes
                    'props_asset_duration' => 'required|numeric|max:' . $max_duration,                              // duration (double) seconds
                    'props_asset_loop' => 'present|in:true,false',                                                  // loop? (true|false)
                    'props_asset_preload' => 'present|in:none,auto',                                                // preload? (none|auto)
                    'props_asset_autoplay' => 'present|in:true,false',                                              // autoplay? (true)
                    'props_asset_file' => 'required|file|mimetypes:audio/mpeg|max:' . $max_filesize,                // mp3, kilobytes
                ];
                break;

            case 'a-gltf-model':
                $validation = [
                    'name' => 'required|string|max:50',
                    'type' => 'required|in:a-image,a-sound,a-video,a-text,a-gltf-model',
                    'marker_id' => 'required|exists:markers,id',
                    'props_asset_type' => 'required|in:model,img,video,audio',
                    'props_asset_size' => 'required|numeric|max:' . $max_filesize*1024,                             // bytes
                    'props_asset_file' => 'required|file|mimetypes:application/octet-stream,text/plain|max:'.$max_filesize,    // extensions: gltf,glb | mimetypes: application/octet-stream, model/gltf+json, model/gltf+binary, model/gltf+buffer
                ];
                break;

            default:
                $validation = [
                    'type' => 'required|in:a-image,a-sound,a-video,a-text,a-gltf-model'
                ];
                break;      
        }
        return $validation;
    }
}
