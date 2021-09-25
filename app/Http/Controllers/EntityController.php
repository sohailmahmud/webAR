<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddEntityRequest;
use App\Http\Requests\EditEntityRequest;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Entity;

class EntityController extends Controller
{

    /**
     * [Attributes]
     * 
     * - `id`
     * - `name`
     * - `type`             // a-image, a-sound, a-video, a-text, a-gltf-model
     * - `props`            // json format
     *  -- `entity`         // primitive properties and components
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
     *    -- `ratio`        // 1.5
     *    -- `preload`      // auto|none
     *    -- `duration`     // seconds (int)
     *    -- `autoplay`     // true|false
     *    -- `loop`         // true|false
     * - scene_id
     * 
     */

     
    public function add(AddEntityRequest $request)
    {
        $this->authorize('add', Entity::class);

        $props = [
            'entity' => [],
            'asset' => []
        ];

        // a-image, a-sound, a-video, a-text, a-gltf-model
        $type = $request->input('type');

        switch ($type) {

            case 'a-image':
                $props['asset']['type'] = $request->input('props_asset_type');
                $props['asset']['width'] = (int) $request->input('props_asset_width');
                $props['asset']['height'] = (int) $request->input('props_asset_height');
                $props['asset']['ratio'] = round($props['asset']['width']/$props['asset']['height'], 2);
                $props['asset']['size'] = (float) $request->input('props_asset_size');
                $props['entity']['width'] =$props['asset']['ratio'];
                $props['entity']['height'] = 1;
                $props['entity']['position'] = ['x' => '0', 'y' => '0', 'z' => '0'];
                $props['entity']['rotation'] = ['x' => '0', 'y' => '0', 'z' => '0'];
                break;

            case 'a-video':
                $props['asset']['type'] = $request->input('props_asset_type');
                $props['asset']['width'] = (int) $request->input('props_asset_width');
                $props['asset']['height'] = (int) $request->input('props_asset_height');
                $props['asset']['ratio'] = round($props['asset']['width']/$props['asset']['height'], 2);
                $props['asset']['size'] = (float) $request->input('props_asset_size');
                $props['asset']['duration'] = (int) $request->input('props_asset_duration');
                $props['entity']['width'] = $props['asset']['ratio'];
                $props['entity']['height'] = 1;
                $props['entity']['position'] = ['x' => 0, 'y' => 0, 'z' => 0];
                $props['entity']['rotation'] = ['x' => 0, 'y' => 0, 'z' => 0];
                break;
            
            case 'a-sound':
                $props['asset']['type'] = $request->input('props_asset_type');
                $props['asset']['size'] = (float) $request->input('props_asset_size');
                $props['asset']['duration'] = (int) $request->input('props_asset_duration');
                $props['entity']['autoplay'] = 'false';
                $props['entity']['on'] = 'null';
                $props['entity']['volume'] = '1';
                $props['entity']['loop'] = 'false';
                break; 

            case 'a-gltf-model':
                $props['asset']['type'] =  $request->input('props_asset_type');
                $props['asset']['size'] = (float) $request->input('props_asset_size');
                $props['entity']['position'] = ['x' => 0, 'y' => 0, 'z' => 0];
                $props['entity']['rotation'] = ['x' => 0, 'y' => 0, 'z' => 0];
                $props['entity']['scale'] = ['x' => 1, 'y' => 1, 'z' => 1];
                break;
        }

        if($type == 'a-sound' || $type == 'a-video') {
            if(!empty($request->input('props_asset_loop'))) {
                $props['asset']['loop'] = $request->input('props_asset_loop');
            }
            if(!empty($request->input('props_asset_preload'))) {
                $props['asset']['preload'] = $request->input('props_asset_preload');
            }
            if(!empty($request->input('props_asset_autoplay'))) {
                $props['asset']['autoplay'] = $request->input('props_asset_autoplay');
            }
        }

        if($request->hasFile('props_asset_file') 
            && $request->file('props_asset_file')->isValid()) {
            //https://github.com/symfony/symfony/blob/3.0/src/Symfony/Component/HttpFoundation/File/UploadedFile.php
            $ext = $request->file('props_asset_file')->getClientOriginalExtension();
            $filename = Str::random(10) . '.' . $ext;
            if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'mp3', 'mpeg', 'mp4', 'gltf', 'glb'])) {
                $path = $request->file('props_asset_file')->storeAs('assets', $filename);
            }

            $props['asset']['ext'] = $ext;
            $props['asset']['file'] = $filename;
            $props['entity']['src'] = Storage::url("assets/$filename");
        }

        $ent = new Entity();
        $ent->name = $request->input('name');
        $ent->type = $type;
        $ent->props = json_encode($props);
        $ent->marker_id = $request->input('marker_id');
        $ent->save();

        $ent->props = $props;
        return response()->json(['response' => 'OK', 'entity' => $ent]);

    }



    public function edit(EditEntityRequest $req, Entity $entity)
    {
        $this->authorize('edit', $entity);
        
        $props = json_decode($entity->props, true);
        

        // --------------------- [Default Fields] --------------------- //

        $entity->name = $req->input('name', $entity->name);


        // -------------------- [Asset Properties] -------------------- //

        $allowed_asset_props = ['preload', 'autoplay', 'loop'];
        $arr_asset = $req->input('asset','');
        if(!empty($arr_asset) && is_array($arr_asset)) {
            foreach ($arr_asset as $k => $v) {
                if(in_array($k, $allowed_asset_props)) {
                    $props['asset'][$k] = $v;
                }
            }
        }


        // ------------------- [Entity Properties] -------------------- //

        $allowed_entity_props = [
            'color', 'height', 'metalness', 'opacity', 
            'roughness', 'shader', 'side', 'transparent', 
            'width', 'autoplay', 'loop', 'on', 'volume'
        ];
        $arr_entity = $req->input('entity','');
        if(!empty($arr_entity) && is_array($arr_entity)) {
            foreach($arr_entity as $k => $v) {
                if(in_array($k, $allowed_entity_props)) {
                    $props['entity'][$k] = $v;
                }
            }
        }


        // ------------------- [Entity Components] -------------------- //

        $allowed_entity_comps = ['position', 'rotation', 'scale', 'chromakey'];
        $arr_entity_comps = $req->input('entity.components','');
        if(!empty($arr_entity_comps) && is_array($arr_entity_comps)) {
            foreach($arr_entity_comps as $k => $v) {
                if(in_array($k, $allowed_entity_comps)) {
                    $props['entity'][$k] = $v;
                }
            }
        }

        
        $entity->props = json_encode($props);
        $entity->save();

        $entity->props = $props;
        return response()->json(['response' => 'OK', 'entity' => $entity]);
    } 



    public function delete(Entity $entity)
    {
        $this->authorize('delete', $entity);
        
        $props = json_decode($entity->props);
        $file = isset($props->asset->file) && !empty($props->asset->file)? $props->asset->file: false;
        if ($file) {
            $path = "assets/$file";
            Storage::delete($path);
        }
        $entity->delete();
        return ['response' => 'OK', 'message' => 'deleted', 'entity' => $entity];
    }


}
