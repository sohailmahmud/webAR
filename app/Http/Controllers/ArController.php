<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scene;
use App\CustomMarker;
use Illuminate\Support\Facades\Storage;

class ArController extends Controller
{
    
    public function browser(Scene $scene)
    {

        $scene->markers[0]->pattern = Storage::url("patterns/{$scene->markers[0]->pattern}");

            // Entities
        $scene->markers[0]->entities = ($scene->markers[0]->entities)->map(function($entity) {
                $entity->props = json_decode($entity->props);
                return $entity;
        });

        /*
            Extensions:
                - ContactBar
                - ButtonCallToAction
        */
        $extensions = $scene->extensions;
        $extensions = $extensions->map(function ($extension) {
            $extension->props = json_decode($extension->props);
            return $extension;
        });

        return view('ar.browser', [
            'scene' => $scene, 
            'entities' => $scene->markers[0]->entities, 
            'marker' => $scene->markers[0],
            'extensions' => $extensions
        ]);

    }



    public function test(CustomMarker $custommarker)
    {
        $custommarker->pattern = Storage::url("custom_markers/$custommarker->pattern");
        return view('ar.test', [ 'custommarker' => $custommarker]);
    }

}
