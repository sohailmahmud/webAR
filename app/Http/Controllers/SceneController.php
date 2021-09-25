<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Scene;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SceneController extends Controller
{

    public function create(Request $request) {
        // create scene
        $scene = new Scene();
        $scene->user_id = $request->user()->id;
        $scene->save();

        return redirect()->action('SceneController@edit', ['id' => $scene->id]);
    }



    public function edit(Scene $scene)
    {
        
        $this->authorize('_edit', $scene);

        // Configs
        $configs['APP_UPLOAD_MAX_FILESIZE'] = config('app.APP_UPLOAD_MAX_FILESIZE'); 
        $configs['APP_UPLOAD_MAX_MEDIA_WIDTH'] = config('app.APP_UPLOAD_MAX_MEDIA_WIDTH'); 
        $configs['APP_UPLOAD_MAX_MEDIA_HEIGHT'] = config('app.APP_UPLOAD_MAX_MEDIA_HEIGHT'); 
        $configs['APP_UPLOAD_MAX_MEDIA_DURATION'] = config('app.APP_UPLOAD_MAX_MEDIA_DURATION');

        return view('scene.edit', ['scene' => $scene, 'configs' => $configs]);
    }



    public function view(Scene $scene)
    {

        $this->authorize('view', $scene);

        // Marker
        if(count($scene->markers) > 0) {

            $scene->markers[0]->pattern = Storage::url("patterns/{$scene->markers[0]->pattern}");
            $scene->markers[0]->image = Storage::url("markers/{$scene->markers[0]->image}");
            $scene->markers[0]->scene_url = url("ar/{$scene->id}");

            // Entities
            $scene->markers[0]->entities = ($scene->markers[0]->entities)->map(function($entity) {
                $entity->props = json_decode($entity->props);
                return $entity;
            });
        }
         
        return response()->json(['response' => 'OK', 'scene' => $scene]);
    }



    public function index(Request $request) {

        $scenes = Scene::where('user_id', $request->user()->id)
                        ->where('type', 's')
                        ->orderBy('created_at', 'desc')
                        ->paginate($this->itens_page);
      
        if($request->query('type') == 'json')
            return response()->json(['response' => 'OK', 'scenes' => $scenes]);
        return view('scene.index', ['scenes' => $scenes]);
    }



    public function admin_index(Request $request) {

        $scenes = Scene::where('type', 's')
                        ->orderBy('created_at', 'desc')
                        ->paginate($this->itens_page);
        
        if($request->query('type') == 'json')
            return response()->json(['response' => 'OK', 'scenes' => $scenes]);
        return view('scene.admin_index', ['scenes' => $scenes]);
    }



    public function addOrEdit(Request $request, Scene $scene=null)
    {
        
        $this->authorize('edit', $scene);

        $validation = [
            'title' => 'present|nullable|string|max:255',
            'description' => 'present|nullable|string',
            'type' => 'required|string|in:s,b',                    // s - single, b - bundle
            'status' => 'required|numeric|in:0,1,2',               // 0 - draft, 1 - published, 2 - archived
            'editable' => 'sometimes|numeric|in:0,1'               // 0 - no, 1 - yes
        ];

        // Publishing
        if($request->status == 1) {
            $validation['title'] = 'required|string|max:255';
            $validation['description'] = 'required|string';
            $validation['published_at'] = 'required|date_format:Y-m-d H:i:s';
        }

        $request->validate($validation);

        $scene->title = $request->title;
        $scene->description = $request->description;
        $scene->type = $request->type;

        // Only admin can archive and unarchive scene
        // make the scene editable or non-editable
        if($request->user()->role == 'admin') {
            if($request->status == 1 && $scene->status != 1) {
                $scene->published_at = $request->published_at;
            }
            $scene->status = $request->status;
            $scene->editable = $request->editable;
        }

        if($request->user()->role == 'editor') {
            if($request->status != 2) {
                if($request->status == 1 && $scene->status != 1) {
                    $scene->published_at = $request->published_at;
                }
                $scene->status = $request->status;
            }
        }

        $scene->save();
        return response()->json(['response' => 'OK', 'scene' => $scene]);
    }


   
    public function delete(Request $request, Scene $scene)
    {

        $this->authorize('delete', $scene);

        foreach ($scene->extensions as $extension) {
            $extension->delete();
        }
        
        if(count($scene->markers)) {

            // Delete Entities
            $entities = $scene->markers[0]->entities;
            if(count($entities)) {
                foreach ($entities as $entity) {
                    $entity->props = json_decode($entity->props);
                    $file = $entity->props->asset->file?? false;
                    if ($file) {
                        Storage::delete("assets/$file");
                    }
                    $entity->delete();
                }
            }

            // Delete Marker
            $marker = $scene->markers[0];
            $image = $marker->image?? false;
            $patt = $marker->pattern?? false;
            if($image) {
                Storage::delete("markers/$image");
            }
            if($patt) {
                Storage::delete("patterns/$patt");
            }
            
            $marker->delete();
    
        }
        
        // Delete Scene
        $scene->delete();
        
        if ($request->query('type') == 'json') {
            return response()->json(['response' => 'OK', 'message' => 'deleted']);
        } else {
            $request->session()->flash('status', __('Scene deleted.'));
            $action = $request->query('action', 'index');
            return redirect()->action('SceneController@' . $action);
        }
        
    }

}
