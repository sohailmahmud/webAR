@extends('layouts.dashboard')
@section('title', __('Settings'))

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-fw fa-cog"></i> {{ __('Settings') }}
    </h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form action="{{ url('/configs') }}" method="POST" enctype="multipart/form-data" id="config-form">
    @csrf

    <!-- THEME -->
    <div class="row">
        <div class="col col-md-12">

            <h2 class="h5">{{ __('Theme') }}</h2>
            <hr>

            <div class="form-group row">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Logo') }}</label>
                <div class="col-sm-6 col-md-4">
                        <label class="col-form-label">
                            <img src="{{ $LOGO_FILE }}" width="400" class="img-thumbnail" id="themelogo-preview">
                        </label>
                            
                    <div class="custom-file">
                        <input type="file" name="APP_LOGO_FILE" class="custom-file-input" id="themelogo" accept="image/png,image/gif,image/jpeg">
                        <label class="custom-file-label" for="themelogo" data-browse="{{ __('Browse') }}">{{ __('Choose file') }}</label>
                        <small id="icon-help" class="form-text text-muted">
                            {{ __('Recommended width between 400px and 800px.') }}
                        </small> 
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Icon') }}</label>
                <div class="col-sm-6 col-md-4">
                    <label class="col-form-label">
                        <img src="{{ $ICON_FILE }}" width="40" class="img-thumbnail" id="themeicon-preview">
                    </label>
                    
                    <div class="custom-file">
                        <input type="file" name="APP_ICON_FILE" class="custom-file-input" id="themeicon" accept="image/png,image/gif,image/jpeg">
                        <label class="custom-file-label" for="themeicon" data-browse="{{ __('Browse') }}">{{ __('Choose file') }}</label>
                        <small id="icon-help" class="form-text text-muted">
                            {{ __('Recommended size 40x40.') }}
                        </small> 
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="themecolor" class="col-sm-3 col-md-2 col-form-label">{{ __('Color') }}</label>
                <div class="col-sm-6 col-md-4"> 
                    <input type="text" name="APP_THEME_COLOR" value="{{ $APP_THEME_COLOR }}" class="form-control" id="themecolor">
                    <input type="color" value="{{ $APP_THEME_COLOR }}" class="form-control" id="themecolor2">           
                </div>
            </div>

        </div>
    </div>


    <!-- COMPONENT LOADING SCREEN (not implemented) -->
    <input type="hidden" name="APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED" value="0">
    <input type="checkbox" id="loading-screen" name="APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED" value="1" {{ $APP_SCENE_COMPONENT_LOADING_SCREEN_ENABLED == '1'? 'checked': ''}} hidden>
    <input type="text" id="loader-title" name="APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE" value="{{ $APP_SCENE_COMPONENT_LOADING_SCREEN_TITLE }}" hidden>      
    <input type="color" id="loader-dotscolor" name="APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR" value="{{ $APP_SCENE_COMPONENT_LOADING_SCREEN_DOTSCOLOR }}" hidden>      
    <input type="color" id="loader-backgroundcolor" name="APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR" value="{{ $APP_SCENE_COMPONENT_LOADING_SCREEN_BACKGROUNDCOLOR }}" hidden>


    <!-- BIT.LY -->
    <div class="row">
        <div class="col col-md-12">

            <h2 class="h5">{{ __('Bit.ly Integration') }}</h2>
            <hr>

            <div class="form-group row ">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Access Token') }}</label>
                <div class="col-sm-6 col-md-4"> 
                    <input type="text" name="BITLY_ACCESS_TOKEN" value="{{ $BITLY_ACCESS_TOKEN }}" id="bitly-text" class="form-control" maxlength="100">          
                </div>
            </div>
                
        </div>
    </div>


    <!-- FILE UPLOAD -->
    <div class="row">
        <div class="col col-md-12">

            <h2 class="h5">{{ __('File Upload') }}</h2>
            <hr>

            <div class="form-group row">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Max upload file size (KB)') }}</label>
                <div class="col-sm-6 col-md-4"> 
                    <input type="number" name="APP_UPLOAD_MAX_FILESIZE" value="{{ $APP_UPLOAD_MAX_FILESIZE }}" class="form-control" min="512" max="{{ $upload_max_filesize }}" step="1">          
                    <small class="form-text text-muted">
                        {{ __('php.ini limit: ') . $upload_max_filesize}}KB
                    </small> 
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Max media width (px)') }}</label>
                <div class="col-sm-6 col-md-4"> 
                    <input type="number" name="APP_UPLOAD_MAX_MEDIA_WIDTH" value="{{ $APP_UPLOAD_MAX_MEDIA_WIDTH }}" class="form-control" min="800" max="5000" step="1">          
                    <small class="form-text text-muted">
                        {{ __('Maximum width of images and videos.') }}
                    </small> 
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Max media height (px)') }}</label>
                <div class="col-sm-6 col-md-4"> 
                    <input type="number" name="APP_UPLOAD_MAX_MEDIA_HEIGHT" value="{{ $APP_UPLOAD_MAX_MEDIA_HEIGHT }}" class="form-control" min="800" max="5000" step="1">          
                    <small class="form-text text-muted">
                        {{ __('Maximum height of images and videos.') }}
                    </small> 
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-md-2 col-form-label">{{ __('Max media duration') }}</label>
                <div class="col-sm-6 col-md-4"> 
                    <input type="number" name="APP_UPLOAD_MAX_MEDIA_DURATION" value="{{ $APP_UPLOAD_MAX_MEDIA_DURATION }}" class="form-control" min="10" max="600" step="1">          
                    <small class="form-text text-muted">
                        {{ __('Maximum time in seconds of sounds and videos.') }}
                    </small> 
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 offset-sm-3 col-md-4 offset-md-2">
            <input type="submit" class="btn btn-primary my-4 ml-5 mx-auto" value="{{ __('Save Changes') }}" >
        </div>
    </div>

</form>

@endsection
