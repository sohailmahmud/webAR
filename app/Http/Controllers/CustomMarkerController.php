<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\CustomMarker;

class CustomMarkerController extends Controller
{

    public function create(Request $request)
    {
        // create custom marker
        $custommarker = new CustomMarker();
        $custommarker->user_id = $request->user()->id;
        $custommarker->save();

        return redirect()->action('CustomMarkerController@edit', ['id' => $custommarker->id]);
    }



    public function edit(Request $request, CustomMarker $custommarker)
    {
        $this->authorize('edit', $custommarker);

        if($request->isMethod('get')) {
            $custommarker->title = $custommarker->title?? "";
            $image = $custommarker->image?? false;
            if($image) {
                $contents = Storage::get("custom_markers/$custommarker->image");
                $custommarker->image = 'data:image/png;base64,' . base64_encode($contents);
            }
            $patt = $custommarker->pattern?? false;
            if($patt) {
                $contents = Storage::get("custom_markers/$custommarker->pattern");
                $custommarker->pattern = $contents;
            }
            $custommarker->validation_url = url("ar-test/{$custommarker->id}");
        }

        if($request->isMethod('put')) {

            $name = Str::random(10);

            // title
            $custommarker->title = $request->input('title');

            // marker image
            $image = $custommarker->image?? false;
            if($image) {
                Storage::delete("custom_markers/$custommarker->image");
            }
            $marker_image = str_replace("data:image/png;base64,", "", $request->input('custom_marker_image'));
            $marker_image = base64_decode($marker_image);
            Storage::put("custom_markers/$name.png", $marker_image);
            $custommarker->image = $name . '.png';

            // marker image thumb
            $thumb = $custommarker->thumb?? false;
            if($thumb) {
                Storage::delete("custom_markers/$custommarker->thumb");
            }
            $marker_image_thumb = str_replace("data:image/png;base64,", "", $request->input('custom_marker_image_thumb'));
            $marker_image_thumb = base64_decode($marker_image_thumb);
            Storage::put("custom_markers/$name-thumb.png", $marker_image_thumb);
            $custommarker->thumb = $name . '-thumb.png';

            // pattern
            $patt = $custommarker->pattern?? false;
            if($patt) {
                Storage::delete("custom_markers/$custommarker->pattern");
            }
            $patt_txt = $request->input('custom_marker_pattern');
            Storage::put("custom_markers/$name.patt", $patt_txt);
            $custommarker->pattern = $name . '.patt';

            $custommarker->save();

            $custommarker->image = "data:image/png;base64," . base64_encode(Storage::get("custom_markers/$custommarker->image"));
            $custommarker->thumb = "data:image/png;base64," . base64_encode(Storage::get("custom_markers/$custommarker->thumb"));
            $custommarker->pattern = Storage::get("custom_markers/$custommarker->pattern");
            $custommarker->validation_url = url("ar-test/{$custommarker->id}");

            $request->session()->flash('status', __('Custom Marker created. Please, check the instructions below.'));

        }

        return view('custom_marker.edit', ['custommarker' => $custommarker]);
    }



    public function index(Request $request)
    {
        
        if ($request->query('type') == 'json') {
            $custom_markers = CustomMarker::where('user_id', $request->user()->id)
                                            ->orderBy('id', 'desc')
                                            ->get();
                                
            $custom_markers = $custom_markers->map(function($custom) {
                $custom->thumb = Storage::url("custom_markers/{$custom->thumb}");
                return $custom;
            });
                                            
            return response()->json(['response' => 'OK', 'custom_markers' => $custom_markers]);
        }

        $custom_markers = CustomMarker::where('user_id', $request->user()->id)
                                        ->orderBy('id', 'desc')
                                        ->paginate($this->itens_page);
        return view('custom_marker.index', ['custom_markers' => $custom_markers]);
        
    }



    public function admin_index()
    {
        $custom_markers = CustomMarker::where('id', '>', 0)
                        ->orderBy('id', 'desc')
                        ->paginate($this->itens_page);
        return view('custom_marker.admin_index', ['custom_markers' => $custom_markers]);
    }



    public function delete(Request $request, CustomMarker $custommarker)
    {
        $this->authorize('delete', $custommarker);

        $image = $custommarker->image?? false;
        if($image) {
            Storage::delete("custom_markers/$image");
            Storage::delete("custom_markers/{$custommarker->pattern}");
            Storage::delete("custom_markers/{$custommarker->thumb}");
        }

        $custommarker->delete();

        $request->session()->flash('status', __('Custom marker deleted.'));
        return redirect()->action('CustomMarkerController@index');
    }


}