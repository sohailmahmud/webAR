<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddEditMarkerRequest;
use App\Http\Requests\DownloadMarkerRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Marker;
use App\CustomMarker;
use App\Config;

use Dompdf\Dompdf;

class MarkerController extends Controller
{

    public function add(AddEditMarkerRequest $req)
    {

        $this->authorize('add', Marker::class);

        $marker = new Marker();

        $sceneId = $req->input('scene_id');
        $sceneURL = url("/ar/$sceneId");

        $markerSourceName = '00';
        $markerDestName = Str::random(10);

        // Save marker image file
        $contents = Storage::get("default_markers/$markerSourceName.png");
        Storage::put("markers/$markerDestName.png", $contents);

        // Save pattern file
        $contents = Storage::get("default_markers/$markerSourceName.patt");
        Storage::put("patterns/$markerDestName.patt", $contents);

        // Generate bitly short url
        $shortUrl = $this->getShortURL($sceneURL);

        // Insert short url under marker
        //ln 127 - https://github.com/endroid/qr-code/blob/master/src/Writer/PngWriter.php
        $sourceImage = imagecreatefrompng(Storage::url("markers/$markerDestName.png"));

        $label = $shortUrl;
        $labelFontSize = '70';
        $labelMarginTop = 0;
        $labelMarginBottom = 50;
        $labelFontPath = public_path('files/noto_sans.otf');

        if (!function_exists('imagettfbbox')) {
            throw new Exception('Missing function "imagettfbbox", please make sure you installed the FreeType library');
        }

        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontPath, $label);
        $labelBoxWidth = intval($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = intval($labelBox[0] - $labelBox[7]);

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight + $labelBoxHeight + $labelMarginTop + $labelMarginBottom;
        
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (!is_resource($targetImage)) {
            throw new Exception('Unable to generate image: check your GD installation');
        }

        $foregroundColor = imagecolorallocate($targetImage, 0, 0, 0);
        $backgroundColor = imagecolorallocate($targetImage, 255, 255, 255);
        imagefill($targetImage, 0, 0, $backgroundColor);

        // Copy source image to target image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);

        $labelX = intval($targetWidth / 2 - $labelBoxWidth / 2);
        $labelY = $targetHeight - $labelMarginBottom;

        imagettftext($targetImage, $labelFontSize, 0, $labelX, $labelY, $foregroundColor, $labelFontPath, $label);

 
        $destination = public_path('files/markers/') . $markerDestName . '.png';

        // Save public disk
        imagepng($targetImage, $destination);    

        $exists = Storage::exists("markers/$markerDestName.png");
        if(!$exists) {
            $contents = Storage::disk('public')->get("markers/$markerDestName.png");
            Storage::put("markers/$markerDestName.png", $contents);
            Storage::disk('public')->delete("markers/$markerDestName");
        }

        // Salva marker e pattern na tabela markers
        $marker->scene_id = $sceneId;
        $marker->image = "$markerDestName.png";
        $marker->pattern = "$markerDestName.patt";
        $marker->save();

        $marker->image = Storage::url("markers/$markerDestName.png");
        $marker->pattern = Storage::url("markers/$markerDestName.patt");
        $marker->scene_url = $sceneURL;
        $marker->scene_short_url = $shortUrl;

        return response()->json(['response' => 'OK', 'marker' => $marker]);

    }



    public function edit(AddEditMarkerRequest $req, Marker $marker)
    {
  
        $this->authorize('edit', $marker);

        /**
         * <option 
         *  data-default="1" // se é marker default ou custom
         *  data-num="00"        // num do marker
         */

        $markerId = $req->input('marker_id'); 
        $sceneId = $req->input('scene_id');
        $isDefault = $req->input('default');
        $num = $req->input('num');
        $sceneURL = url("/ar/$sceneId");

        $marker = Marker::find($markerId);
        Storage::delete("markers/{$marker->image}");
        Storage::delete("patterns/{$marker->pattern}");

        $markerDestName = Str::random(10);

        if($isDefault) {
            $markerSourceName = "$num";

            // Save marker image file
            $contents = Storage::get("default_markers/$markerSourceName.png");
            Storage::put("markers/$markerDestName.png", $contents);

            // Save pattern file
            $contents = Storage::get("default_markers/$markerSourceName.patt");
            Storage::put("patterns/$markerDestName.patt", $contents);
        }
        else {
            $customMark = CustomMarker::find($num);
            $markerSourceName = substr($customMark->image, 0, -4);

             // Save marker image file
            $contents = Storage::get("custom_markers/$markerSourceName.png");
            Storage::put("markers/$markerDestName.png", $contents);

            // Save pattern file
            $contents = Storage::get("custom_markers/$markerSourceName.patt");
            Storage::put("patterns/$markerDestName.patt", $contents);

        }

        // Generate bitly short url
        $shortUrl = $this->getShortURL($sceneURL);

        // Insert short url under marker
        //ln 127 - https://github.com/endroid/qr-code/blob/master/src/Writer/PngWriter.php
        $sourceImage = imagecreatefrompng(Storage::url("markers/$markerDestName.png"));

        $label = $shortUrl;
        $labelFontSize = '70';
        $labelMarginTop = 0;
        $labelMarginBottom = 50;
        $labelFontPath = public_path('files/open_sans.ttf');

        if (!function_exists('imagettfbbox')) {
            throw new Exception('Missing function "imagettfbbox", please make sure you installed the FreeType library');
        }

        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontPath, $label);
        $labelBoxWidth = intval($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = intval($labelBox[0] - $labelBox[7]);

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight + $labelBoxHeight + $labelMarginTop + $labelMarginBottom;
        
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (!is_resource($targetImage)) {
            throw new Exception('Unable to generate image: check your GD installation');
        }

        $foregroundColor = imagecolorallocate($targetImage, 0, 0, 0);
        $backgroundColor = imagecolorallocate($targetImage, 255, 255, 255);
        imagefill($targetImage, 0, 0, $backgroundColor);

        // Copy source image to target image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);

        $labelX = intval($targetWidth / 2 - $labelBoxWidth / 2);
        $labelY = $targetHeight - $labelMarginBottom;

        imagettftext($targetImage, $labelFontSize, 0, $labelX, $labelY, $foregroundColor, $labelFontPath, $label);

 
        $destination = public_path('files/markers/') . $markerDestName . '.png';

        // Save public disk
        imagepng($targetImage, $destination);    

        $exists = Storage::exists("markers/$markerDestName.png");
        if(!$exists) {
            $contents = Storage::disk('public')->get("markers/$markerDestName.png");
            Storage::put("markers/$markerDestName.png", $contents);
            Storage::disk('public')->delete("markers/$markerDestName");
        }

        // Salva marker e pattern na tabela markers
        $marker->scene_id = $sceneId;
        $marker->image = "$markerDestName.png";
        $marker->pattern = "$markerDestName.patt";
        $marker->save();

        $marker->image = Storage::url("markers/$markerDestName.png");
        $marker->pattern = Storage::url("markers/$markerDestName.patt");
        $marker->scene_url = $sceneURL;
        $marker->scene_short_url = $shortUrl;

        return response()->json(['response' => 'OK', 'marker' => $marker]);
    }



    public function download(DownloadMarkerRequest $req, Marker $marker)
    {
        $this->authorize('download', $marker);
        $file = "markers/{$marker->image}";
        $name = 'marker_' . date("Y_m_d_H_i_s");

        // PNG
        if($req->query('download_type') == 'png') {
            if($req->query('preview', '0') == '1') {
                return response(Storage::get($file))                            
                        ->header('Content-Type', 'image/png');
            }
            return Storage::download($file, $name);
        } else {
            // PDF
            $file_datauri = "data:image/png;base64,";
            $file_datauri .= base64_encode(Storage::get($file));

            $quantityOfMarkers = $req->query('quantity_markers', 1);
            $markerSize = $req->query('marker_size', 1);

            // markerSize => Columns, ImageWidth (px)
            $table = [[4, 170], [3, 230], [2, 270], [2, 350], [1, 400]];

            $cols = $table[$markerSize - 1][0];
            $lines = ceil($quantityOfMarkers/$cols);
            $imgWidth = $table[$markerSize - 1][1];
            
            $html =   "<!DOCTYPE html><html><head><title>$name</title>";
            $html .= '<link href="' . asset('css/custom.css') .'" rel="stylesheet">';
            $html .= '</head><body>';
            $html .=  '<table class="pdf-w714"><tbody>';

            for ($i=0; $i < $lines; $i++) {
                $html .= '<tr>';
                for ($j=0; $j < $cols; $j++) { 
                    if($quantityOfMarkers > 0) {
                        $html .= '<td class="pdf-td">';
                        $html .= '<img style="width: '.$imgWidth.'px;" src="'.$file_datauri.'" />';
                        $html .= '</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $quantityOfMarkers--;
                }
                $html .= '</tr>';
            }

            $html .= "</tbody></table></body></html>";
            
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render(); // Render the HTML as PDF
            if($req->query('preview') == '1') {
                $output = $dompdf->output();                        // PDF content
                return response($output)                            // Displays the PDF in the browser
                        ->header('Content-Type', 'application/pdf');
            }
            return $dompdf->stream($name); // Download
        }
    }



    private function getShortURL($longURL) {
    
        $access_token = config('app.BITLY_ACCESS_TOKEN');
        
        if(empty($access_token)) {
            return $longURL;
        }

        // https://artisansweb.net/create-tinyurl-using-bitly-api-php/
        $url = 'https://api-ssl.bitly.com/v4/bitlinks';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['long_url' => $longURL])); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $access_token",
            "Content-Type: application/json"
        ]);
        
        $arr_result = json_decode(curl_exec($ch));
        $short_url = $arr_result->link?? $longURL;
        $short_url = str_replace(['http://', 'https://'], '', $short_url);
        return $short_url;
    }

}
