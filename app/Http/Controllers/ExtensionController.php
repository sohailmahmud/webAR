<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExtensionContactForm;
use App\Extension;
use App\User;

class ExtensionController extends Controller
{
    
    public function store(Request $request, Extension $extension = null)
    {
        if($extension) {
            $this->authorize('edit', $extension);
        }
        // Validation
        $rules = [
            'id' => ['sometimes', 'numeric'],
            'type' => ['required', 'string', 'max:50'],
            'props' => ['required', 'json'],
            'scene_id' => ['required', 'numeric', 'exists:scenes,id']
        ];
        $request->validate($rules);
        
        if(!$extension) {
            $extension = new Extension();
        }
        $extension->type = $request->type;
        $extension->props = strip_tags($request->props);
        $extension->scene_id = $request->scene_id;
        $extension->save();

        return $extension;
    }



    public function delete(Request $request, Extension $extension)
    {
        $this->authorize('delete', $extension);
        $extension->delete();
        return ['response' => 'OK'];
    }



    public function extensionsByScene(Request $request, $scene_id)
    {
        $extensions = Extension::where('scene_id', $scene_id)->get();
        return $extensions;
    }


}
