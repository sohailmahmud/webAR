<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Utils\Helper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);

        // app key
        // if(!isset($configs['APP_KEY'])) {
        //     Helper::config('APP_KEY', 'OK');
        //     Artisan::call('key:generate');
        // }

        // icon
        $appIconFile = Helper::config('APP_ICON_FILE');
        $icon = is_null($appIconFile) || empty($appIconFile)? 'icon-holograma.png': $appIconFile;
        Helper::config('APP_ICON_FILE', $icon);
        View::share('ICON_FILE', url('files/' . $icon));


        // logo
        $appLogoFile = Helper::config('APP_LOGO_FILE');
        $logo = is_null($appLogoFile) || empty($appLogoFile)? 'logo-holograma.png': $appLogoFile;
        Helper::config('APP_LOGO_FILE', $logo);
        View::share('LOGO_FILE', url('files/' . $logo));

        // theme
        $appThemeColor = Helper::config('APP_THEME_COLOR');
        $theme_color = is_null($appThemeColor) || empty($appThemeColor)? '#4e73df': $appThemeColor;
        Helper::config('APP_THEME_COLOR', $theme_color);
        View::share('THEME_COLOR', $theme_color);

        // COMPONENT LOADIGN (not implemented)
        $APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED');
        if(is_null($APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED)) {
            logger('1');
            Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED', false);
        }
        $APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE');
        if(is_null($APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE)) {
            Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE', 'Title');
        }
        $APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR');
        if(is_null($APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR)) {
            Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR', '#FFFFFF');
        }
        $APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR = Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR');
        if(is_null($APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR)) {
            Helper::config('APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR', '#888888');
        }      
        // END - COMPONENT LOADIGN (not implemented)

        $APP_UPLOAD_MAX_FILESIZE = Helper::config('APP_UPLOAD_MAX_FILESIZE');
        if(is_null($APP_UPLOAD_MAX_FILESIZE)) {
            Helper::config('APP_UPLOAD_MAX_FILESIZE', 10240);
        }
        $APP_UPLOAD_MAX_MEDIA_WIDTH = Helper::config('APP_UPLOAD_MAX_MEDIA_WIDTH');
        if(is_null($APP_UPLOAD_MAX_MEDIA_WIDTH)) {
            Helper::config('APP_UPLOAD_MAX_MEDIA_WIDTH', 3840);
        }
        $APP_UPLOAD_MAX_MEDIA_HEIGHT = Helper::config('APP_UPLOAD_MAX_MEDIA_HEIGHT');
        if(is_null($APP_UPLOAD_MAX_MEDIA_HEIGHT)) {
            Helper::config('APP_UPLOAD_MAX_MEDIA_HEIGHT', 2160);
        }
        $APP_UPLOAD_MAX_MEDIA_DURATION = Helper::config('APP_UPLOAD_MAX_MEDIA_DURATION');
        if(is_null($APP_UPLOAD_MAX_MEDIA_DURATION)) {
            Helper::config('APP_UPLOAD_MAX_MEDIA_DURATION', 600);
        }
        $BITLY_ACCESS_TOKEN = Helper::config('BITLY_ACCESS_TOKEN');
        if(is_null($BITLY_ACCESS_TOKEN)) {
            Helper::config('BITLY_ACCESS_TOKEN', '');
        }

        // Settings app 
        config([
            //'app.APP_KEY' => Helper::config('APP_KEY'),
            'app.APP_ICON_FILE' => Helper::config('APP_ICON_FILE'),
            'app.APP_LOGO_FILE' => Helper::config('APP_LOGO_FILE'),
            'app.APP_THEME_COLOR' => Helper::config('APP_THEME_COLOR'),
            'app.APP_UPLOAD_MAX_FILESIZE' => Helper::config('APP_UPLOAD_MAX_FILESIZE'),
            'app.APP_UPLOAD_MAX_MEDIA_WIDTH' => Helper::config('APP_UPLOAD_MAX_MEDIA_WIDTH'),
            'app.APP_UPLOAD_MAX_MEDIA_HEIGHT' => Helper::config('APP_UPLOAD_MAX_MEDIA_HEIGHT'),
            'app.APP_UPLOAD_MAX_MEDIA_DURATION' => Helper::config('APP_UPLOAD_MAX_MEDIA_DURATION'),
            'app.BITLY_ACCESS_TOKEN' => Helper::config('BITLY_ACCESS_TOKEN'),
        ]);

    }
}
