<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class EditEntityRequest extends FormRequest
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
        /**
         * [Attributes]
         * 
         * - `id`
         * - `name`
         * - `type`             // a-image, a-sound, a-video, a-text, a-gltf-model
         * - `props`            // json format
         *  -- `entity`         // entity properties and components
         *    -- `src`          // url or id (#elementId)
         *    -- `width`        // meters (double)
         *    -- `height`       // meters (double)
         *    -- `position`   
         *    -- `rotation`  
         *    -- `opacity`
         *    -- ....
         *  -- `asset`          // file and media properties
         *    -- `file`         // filename.ext
         *    -- `type`         // img, video, audio, model
         *    -- `ext`          // mp4, mp3, jpg, png, gif, gltf
         *    -- `size`         // file size in kilobytes (float)
         *    -- `width`        // px (int)
         *    -- `height`       // px (int)
         *    -- `preload`      // auto|none
         *    -- `duration`     // seconds (int)
         *    -- `autoplay`     // true|false
         *    -- `loop`         // true|false
         * - scene_id
         * 
         */


        $validation = [

            /*
            |--------------------------------------------------------------------------|
            | [Default Validation] - `name`, `type`, `scene_id` table fields           |
            |--------------------------------------------------------------------------|
            */

            'name' => 'sometimes|string|max:50',
            'type' => 'required|in:a-image,a-sound,a-video,a-text,a-gltf-model', //hidden
            'marker_id' => 'required|exists:markers,id',


            /*
            |--------------------------------------------------------------------------|
            | [Asset Validation] - `props`.asset - file and media properties           |
            |--------------------------------------------------------------------------|
            */

            'asset.preload' => 'sometimes|string|in:none,auto',                             
            'asset.autoplay' => 'sometimes|string|in:true,false',                            
            'asset.loop' => 'sometimes|string|in:true,false',


            /*
            |--------------------------------------------------------------------------|
            | [Entity Validation] - `props`.entity - entities properties & components  |
            |--------------------------------------------------------------------------|
            */

            //------------------ ENTITIES ------------------//

            // <a-image>, <a-video>

            'entity.color' => [                                     // default: #FFF
                'sometimes',
                'regex:/^((0x){0,1}|#{0,1})([0-9A-F]{8}|[0-9A-F]{6}|[0-9A-F]{3})$/i'
            ],
            'entity.height' => 'sometimes|numeric|min:0.1|max:1000',  // default: 1
            'entity.metalness' => 'sometimes|numeric|min:0|max:1',  // default: 0
            'entity.opacity' => 'sometimes|numeric|min:0|max:1',    // default: 1
            'entity.roughness' => 'sometimes|numeric|min:0|max:1',  // default: 0.5
            'entity.shader' => 'sometimes|in:standard,flat',        // default: flat
            'entity.side' => 'sometimes|in:front,back,double',      // default: front
            'entity.transparent' => 'sometimes|in:true,false',      // default: false
            'entity.width' => 'sometimes|numeric|min:0.1|max:1000',   // default: 1

            // <a-sound>

            'entity.autoplay' => 'sometimes|in:true,false',         // default: false
            'entity.loop' => 'sometimes|in:true,false',             // default: false
            'entity.on' => 'sometimes|in:click,null',                    // default: null
            'entity.volume' => 'sometimes|numeric|min:1|max:100',   // default: 1

            // <a-gltf-model> - Nothing


            //------------------ COMPONENTS ------------------//

            // IMPLEMENTED: POSITION, ROTATION, SCALE, CHROMAKEY

            // [POSITION]
            'entity.components.position.x' => 'sometimes|numeric',
            'entity.components.position.y' => 'sometimes|numeric',
            'entity.components.position.z' => 'sometimes|numeric',

            // [ROTATION]
            'entity.components.rotation.x' => 'sometimes|numeric|min:-360|max:360', // pitch (x)
            'entity.components.rotation.y' => 'sometimes|numeric|min:-360|max:360', // yaw (y)
            'entity.components.rotation.z' => 'sometimes|numeric|min:-360|max:360', // roll (z)

            // [SCALE]
            'entity.components.scale.x' => 'sometimes|numeric|min:0.01|max:1000',
            'entity.components.scale.y' => 'sometimes|numeric|min:0.01|max:1000',
            'entity.components.scale.z' => 'sometimes|numeric|min:0.01|max:1000',

            // [LOADING SCREEN] (not implemented)
            // ConfigController

            // [ANIMATION] (not implemented)
            // https://aframe.io/docs/0.9.0/components/animation.html

            // [CHROMA KEY]
            'entity.components.chromakey' => 'sometimes|numeric|in:0,1',
        ];


        return $validation;
    }

}
