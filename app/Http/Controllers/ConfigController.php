<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Utils\Helper;


class ConfigController extends Controller
{

    private $opts = [

        // THEME
        'APP_ICON_FILE',
        'APP_LOGO_FILE',
        'APP_THEME_COLOR',

        // SCENE
        'APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED',               // X (not implemented)
        'APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE',                 // X (not implemented)
        'APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR',             // X (not implemented)
        'APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR',       // X (not implemented)

        // FILE UPLOAD
        'APP_UPLOAD_MAX_FILESIZE',                                  // 2048 (2MB in KB)
        'APP_UPLOAD_MAX_MEDIA_WIDTH',                               // 3840 (4K width in px)
        'APP_UPLOAD_MAX_MEDIA_HEIGHT',                              // 2160 (4K height in px)
        'APP_UPLOAD_MAX_MEDIA_DURATION',                            // 600  (600s - 10min)

        // BIT.LY
        'BITLY_ACCESS_TOKEN'
    ];


    
    public function index(Request $request)
    {
 
        $this->authorize('index', Config::class);
        $arr_config = [];
        if($request->isMethod('post')) {
            $validation = [
                'APP_ICON_FILE' => '', // 4MB
                'APP_LOGO_FILE' => '', // 4MB
                'APP_THEME_COLOR' => '',
                'APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED' => 'required|numeric|in:0,1',              
                'APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE' => 'sometimes|string|max:100',
                'APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR' => ['sometimes', 'regex:/^((0x){0,1}|#{0,1})([0-9A-F]{8}|[0-9A-F]{6}|[0-9A-F]{3})$/i'],
                'APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR' => ['sometimes', 'regex:/^((0x){0,1}|#{0,1})([0-9A-F]{8}|[0-9A-F]{6}|[0-9A-F]{3})$/i'],
                'APP_UPLOAD_MAX_FILESIZE' => 'required|numeric|min:512|max:102400', // 100MB
                'APP_UPLOAD_MAX_MEDIA_WIDTH' => 'required|numeric|min:800|max:5000',           
                'APP_UPLOAD_MAX_MEDIA_HEIGHT' => 'required|numeric|min:800|max:5000',          
                'APP_UPLOAD_MAX_MEDIA_DURATION' => 'required|numeric|min:10|max:600', // 10 min
                'BITLY_ACCESS_TOKEN' => 'required|string|max:100',        
            ];

            if($request->hasFile('APP_ICON_FILE')) {
                $validation['APP_ICON_FILE'] = 'file|mimes:png,jpeg,gif|max:4096';
            }

            if($request->hasFile('APP_LOGO_FILE')) {
                $validation['APP_LOGO_FILE'] = 'file|mimes:png,jpeg,gif|max:4096';
            }

            if($request->input('APP_THEME_COLOR')) {
                $validation['APP_THEME_COLOR'] = ['present', 'regex:/^((0x){0,1}|#{0,1})([0-9A-F]{8}|[0-9A-F]{6}|[0-9A-F]{3})$/i'];
            }
            
            $request->validate($validation);

            // SAVE ICON FILE
            if($request->hasFile('APP_ICON_FILE') && $request->file('APP_ICON_FILE')->isValid()) {
                if(!empty(Helper::config('APP_ICON_FILE'))) {
                    Storage::disk('public')->delete(Helper::config('APP_ICON_FILE'));
                }
                $ext = $request->file('APP_ICON_FILE')->getClientOriginalExtension();
                $filename = 'icon.' . $ext;
                $request->file('APP_ICON_FILE')->storeAs('/', $filename, 'public');
                Helper::config('APP_ICON_FILE', $filename);
                $arr_config['ICON_FILE'] = Storage::disk('public')->url($filename);
            }

            // SAVE LOGO FILE
            if($request->hasFile('APP_LOGO_FILE') && $request->file('APP_LOGO_FILE')->isValid()) {
                if(!empty(Helper::config('APP_LOGO_FILE'))) {
                    Storage::disk('public')->delete(Helper::config('APP_LOGO_FILE'));
                }
                $ext = $request->file('APP_LOGO_FILE')->getClientOriginalExtension();
                $filename = 'logo.' . $ext;
                $request->file('APP_LOGO_FILE')->storeAs('/', $filename, 'public');
                Helper::config('APP_LOGO_FILE', $filename);
                $arr_config['LOGO_FILE'] = Storage::disk('public')->url($filename);
            }


            $tc = $request->input('APP_THEME_COLOR');
            $arr_config['THEME_COLOR'] = $tc? $tc: '#4e73df';

            // SAVE DYNAMIC CSS FILE
            $logo_file = Helper::config('APP_LOGO_FILE');
            $logo_file = empty($logo_file)? 'logo-holograma.png': $logo_file;
            $logo_url = Storage::disk('public')->url($logo_file);
            $css = <<<CSSF
                .dashboard_bg { 
                    background: url("{$logo_url}") center center no-repeat; 
                    background-size: 50%;
                }
                .holograma-btn, .holograma-btn:hover, .holograma-btn:focus {
                    color: #fff!important; 
                    background-color: {$arr_config['THEME_COLOR']}!important; 
                    border-color: {$arr_config['THEME_COLOR']}!important; 
                    box-shadow: none!important;
                }
                .holograma-color { 
                    color: {$arr_config['THEME_COLOR']}!important; 
                }
                .holograma-backgroundcolor { 
                    background: {$arr_config['THEME_COLOR']}!important; 
                }
CSSF;
            Storage::disk('public')->put('dynamic.css', $css);

            // UPDATE CONFIGS FILE
            $options = $request->all();
            foreach ($options as $k => $v) {
                if(in_array($k, $this->opts) && $k != 'APP_ICON_FILE' && $k != 'APP_LOGO_FILE') {
                    $v = is_null($v) || empty($v)? '': $v;
                    Helper::config($k, $v);
                }
            }
            $request->session()->flash('status', __('Settings saved.'));
        }
        
        
        $arr_config['APP_ICON_FILE'] = Helper::config('APP_ICON_FILE');
        $arr_config['APP_LOGO_FILE'] = Helper::config('APP_LOGO_FILE');
        $arr_config['APP_THEME_COLOR'] = Helper::config('APP_THEME_COLOR');
        $arr_config['APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED'] = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED');
        $arr_config['APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE'] = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE');
        $arr_config['APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR'] = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR');
        $arr_config['APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR'] = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR');
        $arr_config['APP_UPLOAD_MAX_FILESIZE'] = Helper::config('APP_UPLOAD_MAX_FILESIZE');
        $arr_config['APP_UPLOAD_MAX_MEDIA_WIDTH'] = Helper::config('APP_UPLOAD_MAX_MEDIA_WIDTH');
        $arr_config['APP_UPLOAD_MAX_MEDIA_HEIGHT'] = Helper::config('APP_UPLOAD_MAX_MEDIA_HEIGHT');
        $arr_config['APP_UPLOAD_MAX_MEDIA_DURATION'] = Helper::config('APP_UPLOAD_MAX_MEDIA_DURATION');
        $arr_config['BITLY_ACCESS_TOKEN'] = Helper::config('BITLY_ACCESS_TOKEN');

        // Get upload_max_filesize in bytes
        $upLim = strtoupper(ini_get('upload_max_filesize'));
        $num = str_replace(['K', 'M', 'G'], '', $upLim);
        $end = substr($upLim, -1); //M K G
        switch ($end) {
            case 'K':
                $num *= 1024;
                break;
            case 'M':
                $num *= 1024**2;
                break;
            case 'G':
                $num *= 1024**3;
                break;
        }

        $arr_config['upload_max_filesize'] = $num/1024;
        
        return view('config.index', $arr_config);
    }

}
