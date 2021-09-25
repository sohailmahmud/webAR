@extends('layouts.dashboard')
@section('title', __('Create Scene'))

@section('content')

@push('scripts_head')
  <script src="{{ asset('js/ar/aframe.js') }}"></script>
  <script src="{{ asset('js/ar/aframe-components.js') }}"></script>
@endpush


{{-- Wrapper Scene Page --}}
<div id="scene-page">

  {{-- Page Heading --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-cube"></i> {{ __('Create Scene') }}
    </h1>
  </div>

  {{-- Scene Info --}}
  <div class="row">
    <div class="col-md-4">
      <p>
        <b>{{ __('Created') }}</b>: 
        {{ substr($scene->created_at, 0, 16) }}
      </p>
    </div>
    <div class="col-md-4">
      <?php 
        $draft = __('draft');
        $published = __('published');
        $archived = __('archived');
        $status = [$draft, $published, $archived];  
      ?>
      <p>
        <b>{{ __('Status') }}</b>: 
        <span 
          id="info-status" 
          data-draft="{{ $draft }}" 
          data-published="{{ $published }}" 
          data-archived="{{ $archived }}">
          {{ $status[$scene->status] }}
        </span>
      </p>
    </div>
    <div class="col-md-4">
      <p>
        <b>{{ __('Published') }}</b>: 
        <span id="info-published">{{ $scene->published_at? substr($scene->published_at, 0,16): __('no') }}</span>
      </p>
    </div>
  </div>

  
  {{-- Begin Left and Right Side --}}  
  <div class="row">

    {{-- Begin Left Side --}}
    <div class="col col-md-8">

      {{-- Scene --}}
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div id="scene-container">
                {{-- <button type="button" class="btn btn-light btn-sm" id="btnChangeView"><i class="fas fa-tv"></i> {{ __('Change View') }}</button> --}}
                <a-scene embedded vr-mode-ui="enabled: false" id="scene">
                  <a-assets></a-assets>
                  <a-sky src="{{ url('files/bg.jpg') }}" color="#ECECEC"></a-sky>
                  <a-plane 
                    src="{{ url('files/marker-holograma.png') }}" 
                    position="0 0.75 -1.75"
                    rotation="-90 0 0"
                    width="1"
                    height="1"
                    color="#FFFFFF">
                  </a-plane>
                  <a-light type="ambient" color="#fff"></a-light>
                  <a-light type="directional" color="#fff" intensity="0.3" position="-0.5 1 1"></a-light>
                </a-scene>
              </div>
            </div>
          </div>

          {{-- btn play pause --}}
          <div class="mx-auto w40">
            <button id="btn-play-pause" class="btn btn-outline-danger btn-circle mt-2" hidden>
              <i class="fas fa-play"></i> <i class="fas fa-pause"></i>
            </button>
          </div>
          
        </div>
      </div>


      {{-- Form --}}
      <div class="row mt-3">
        <div class="col-md-12">
          <form>
            <input type="hidden" name="scene_id" id="scene_id" value="{{ $scene->id }}">
            <input type="hidden" name="published_at" id="published_at" value="{{ $scene->published_at }}">
            <div class="form-group">
              <label for="title">{{ __('Title') }}</label>
              <input type="text" class="form-control" id="title" value="{{ $scene->title }}" maxlength="150" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="description">{{ __('Description') }}</label>
              <textarea class="form-control" id="description" rows="10">{{ $scene->description }}</textarea>
            </div>
          </form>
        </div>
      </div>

      {{-- Save --}}

      @if(Auth::user()->role == 'admin' && Auth::user()->id != $scene->user_id)
            <div class="row">
              <div class="col">
                <div class="form-check form-check-inline mb-3 float-right">
                  <input class="form-check-input" type="checkbox" id="editable" value="0" {{ $scene->editable? "": "checked" }}>
                  <label class="form-check-label" for="editable">{{ __('The editor can not change the scene') }}</label>
                </div>
              </div>
            </div>
      @endif

      {{-- Role editor and Scene archived or not editable --}}
      @if (Auth::user()->role == 'editor' && (!$scene->editable || $scene->status == 2))
          <div class="row my-4">
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                  {{ __('This scene can not be modified.') }}
                </div>
            </div>
          </div>
      @else

        {{-- SAVE SCENE --}}
        <div class="row my-4">
            <div class="col-sm-8 col-md-8">
              <div class="row">
                <div class="col-sm-6 col-md-6 text-right">
                  <label>{{ __('Scene status:') }}</label>
                </div>
                <div class="col-sm-6 col-md-6">
                  <select class="form-control" id="status">
                    <option value="0" {{ $scene->status == 0? 'selected': '' }}>{{ __('On Draft') }}</option>
                    <option value="1" {{ $scene->status == 1? 'selected': '' }}>{{ __('Public') }}</option>
                    @if(Auth::user()->role == 'admin')
                      <option value="2" {{ $scene->status == 2? 'selected': '' }}>{{ __('Archive') }}</option>
                    @endif
                  </select>
                </div>
              </div>        
            </div>
            <div class="col-sm-4 col-md-4">
              <button class="btn btn-primary btn-block holograma-btn" id="save-scene">
                {{ __('Save') }}
              </button>

              <div class="d-flex justify-content-center">
                <div id="spinner" class="spinner-border spinner-border-sm mt-2" role="status" hidden>
                  <span class="sr-only">{{ __('Loading...') }}</span>
                </div>
                <div id="saved-ok" class="mt-2" hidden>
                  <i class="fas fa-check text-success"></i>
                  <small>{{ __('Saved') }}</small>
                </div>
              </div>

            </div>
        </div>
     
        {{-- DELETE SCENE --}}
        <div class="row">
              <div class="col text-right my-3">
                <form 
                  id="form-delete-scene"
                  method="POST" 
                  action="{{ url('/scenes', ['scene' =>  $scene->id ]) }}" 
                  data-message="{{ __('Do you want to delete the scene?') }}">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-link text-danger">
                    <i class="far fa-trash-alt"></i> {{ __('delete scene') }}
                  </button>
                </form>
              </div>
        </div>

      @endif

    </div>
    {{-- End Left Side --}}


    {{-- Begin Right Side --}}
    <div class="col col-md-4">

      {{-- Btn Add Entity --}}
      <div class="btn-group mb-3 w100p">
          <button type="button" class="btn dropdown-toggle holograma-btn" id="dropdownMenuEntity" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-images"></i> {{ __('Add Entity') }}
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#" data-type="image" data-accept="image/png, image/jpeg, image/gif"><i class="fas fa-fw fa-table"></i> {{ __('Image') }}</a>
            <a class="dropdown-item" href="#" data-type="video" data-accept="video/mp4"><i class="fas fa-fw fa-table"></i> {{ __('Video') }}</a>
            <a class="dropdown-item" href="#" data-type="audio" data-accept="audio/mpeg"><i class="fas fa-fw fa-table"></i> {{ __('Audio') }}</a>
            <a class="dropdown-item" href="#" data-type="model" data-accept=".gltf,.glb"><i class="fas fa-fw fa-table"></i> {{ __('3D Model') }}</a>
          </div>
          {{-- File validation --}}
          <div id="file-validation" hidden
            data-maxwidth="{{ $configs['APP_UPLOAD_MAX_MEDIA_WIDTH'] }}" data-maxwidthmsg="{{ __('Maximum file width: ') . $configs['APP_UPLOAD_MAX_MEDIA_WIDTH'] . 'px' }}"
            data-maxheight="{{ $configs['APP_UPLOAD_MAX_MEDIA_HEIGHT'] }}" data-maxheightmsg="{{ __('Maximum file height: ') . $configs['APP_UPLOAD_MAX_MEDIA_HEIGHT'] . 'px' }}"
            data-maxsize="{{ $configs['APP_UPLOAD_MAX_FILESIZE'] * 1024 }}" data-maxsizemsg="{{ __('Maximum file size: ') . (round($configs['APP_UPLOAD_MAX_FILESIZE']/1024, 1)) . 'MB' }}"
            data-maxduration="{{ $configs['APP_UPLOAD_MAX_MEDIA_DURATION'] }}" data-maxdurationmsg="{{ __('Maximum file duration: ') . ($configs['APP_UPLOAD_MAX_MEDIA_DURATION']) . 's' }}">
          </div>
      </div>

      {{-- Entities --}}
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <a href="#collapseCardEntities" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardEntities">
              <h6 class="m-0 font-weight-bold text-primary holograma-color">{{ __('Entities') }}</h6>
            </a>
            <div class="collapse show" id="collapseCardEntities">
              <div class="card-body">

                {{-- Progress Bar --}}
                <div id="progress-bar-container" class="progress mb-3" hidden>
                  <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                {{-- Entities Forms --}}
                <div id="entities"></div>

              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Marker --}}
      <div class="row">
        <div class="col-md-12 mt-4">
          <div class="card">
            <a href="#collapseCardMarker" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardMarker">
              <h6 class="m-0 font-weight-bold text-primary holograma-color">{{ __('Marker') }}</h6>
            </a>
            <div class="collapse show" id="collapseCardMarker">
              <div class="card-body">
                  <img src="" id="marker_image" class="img-thumbnail">
                  <p class="mt-2 text-secondary text-center"><small id="marker_url"></small></p>
                  <button type="button" id="btn-change-marker" class="btn btn-primary btn-block holograma-btn" data-toggle="modal" data-target="#changeMarkerModal">{{ __('Change Marker') }}</button>
                <hr>
                  <form class="mt-3" id="marker-form">
                    <input type="hidden" name="marker_id" id="marker_id" value="">
                    <div class="row">
                      <div class="col-md-6">
                        <label>{{ __('Save as') }}:</label>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="download_type" id="download_type_pdf" value="pdf" checked>
                          <label class="form-check-label" for="download_type_pdf">PDF</label>
                        </div>
                        <div class="form-group form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="download_type" id="download_type_png" value="png">
                          <label class="form-check-label" for="download_type_png">PNG</label>
                        </div>
                      </div>
                    </div>

                    <div class="row" id="qrcode_print_row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>{{ __('Print') }}:</label>
                          <input type="number" name="quantity_markers" value="1" id="quantity_markers" class="form-control form-control-sm" min="1" max="20" step="1">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="marker_size">{{ __('Size') }}:</label>
                          <select class="form-control form-control-sm" id="marker_size">
                            <option selected>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row mt-2">
                      <div class="col-md-6">
                        <a href="#" id="qrcode_preview" class="btn btn-success btn-sm btn-block">{{ __('Preview') }}</a>
                      </div>
                      <div class="col-md-6">
                        <a href="#" id="qrcode_download" class="btn btn-primary btn-sm btn-block">{{ __('Download') }}</a>
                      </div>
                    </div>
                  </form>      
              </div>
            </div>
          </div>
        </div>
      </div>


      {{-- Extensions Section --}}
      <div class="row">
        <div class="col-md-12 mt-4">
          <div class="card">
            <a href="#collapseCardExtensions" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardMarker">
              <h6 class="m-0 font-weight-bold text-primary holograma-color">{{ __('Extensions') }}</h6>
            </a>
            <div class="collapse show" id="collapseCardExtensions">
              <div class="card-body">

                <div id="extensionsContainer">
                  <p>{{__('Choose and add extensions to make your scene more interactive.')}}</p>
                </div>

                <button type="button" id="btnChooseExtension" data-toggle="modal" data-target="#chooseExtensionModal" class="btn btn-primary btn-block  holograma-btn">
                    <i class="fas fa-puzzle-piece"></i> {{ __('Choose an Extension') }}
                </button>
                        
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    {{-- End Right Side --}}

  </div>
  {{-- End Left and Right Side --}}


</div>
{{-- Wrapper Scene Page --}}


{{-- #changeMarker Modal --}}

<!-- Modal -->
<div class="modal fade" id="changeMarkerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalScrollableTitle">{{ __('Select a Marker') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col col-md-3">
              <div class="form-check">
                <input class="form-check-input custom-marker" type="radio" id="00" name="marker" value="00" data-default="1" data-num="00">
                <label class="form-check-label" for="00">
                  <img src="{{asset("files/default_markers/00-thumb.jpg")}}" class="img-fluid">
                </label>
              </div>
            </div>
            <div class="col col-md-3">
              <div class="form-check">
                <input class="form-check-input custom-marker" type="radio" id="01" name="marker" value="01" data-default="1" data-num="01">
                <label class="form-check-label" for="01">
                  <img src="{{asset("files/default_markers/01-thumb.jpg")}}" class="img-fluid">
                </label>
              </div>
            </div>
            <div class="col col-md-3">
              <div class="form-check">
                <input class="form-check-input custom-marker" type="radio" id="02"  name="marker" value="02" data-default="1" data-num="02">
                <label class="form-check-label" for="02">
                  <img src="{{asset("files/default_markers/02-thumb.jpg")}}" class="img-fluid">
                </label>
              </div>
            </div>
            <div class="col col-md-3">
              <div class="form-check">
                <input class="form-check-input custom-marker" type="radio" id="03"  name="marker" value="03" data-default="1" data-num="03">
                <label class="form-check-label" for="03">
                  <img src="{{asset("files/default_markers/03-thumb.jpg")}}" class="img-fluid">
                </label>
              </div>
            </div>
          </div>
          <div id="custom-markers"></div>
        </form>
      </div>
      <div class="modal-footer">
        <a id="btn-create-custom-marker" class="btn btn-primary holograma-btn mr-auto" href="{{ url("custommarker/create") }}" target="_blank" role="button">{{ __('Create a Custom Marker') }}</a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" id="btn-select-marker" class="btn btn-primary"  data-dismiss="modal">{{ __('Select Marker') }}</button>
      </div>
    </div>
  </div>
</div>


{{-- Matrix forms (entities) --}}
<div id="matrix" hidden>

  {{-- a-image and a-video form matrix (entities) --}}
  <details class="mb-2 entity entity-form a-image a-video" hidden>
      <summary><small>{{ __('New Entity') }}</small></summary>
      <form class="mt-2">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="type" value="">
        <input type="hidden" name="marker_id" value="">
        <input type="hidden" name="entity[color]" value="#FFFFFF">

        {{-- only video --}}
        <input type="hidden" name="asset[preload]" value="none">
        <input type="hidden" name="asset[autoplay]" value="false">
        <input type="hidden" name="asset[loop]" value="false">

        <div class="row">
          <div class="col-md-4">
            <label>{{ __('Name') }}:</label>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" name="name" value="" class="form-control form-control-sm" autocomplete="off">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>{{ __('Width') }}:</label>
              <input type="number" name="entity[width]" value="" class="form-control form-control-sm" min="0.1" max="1000" step="0.01">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>{{ __('Height') }}:</label>
              <input type="number" name="entity[height]" value="" class="form-control form-control-sm" min="0.1" max="1000" step="0.01">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label>{{ __('Position') }}:</label>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][position][x]" value="" class="form-control form-control-sm" min="-100" max="100" step="0.05">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][position][y]" value="" class="form-control form-control-sm" min="-100" max="100" step="0.05">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][position][z]" value="" class="form-control form-control-sm" min="-100" max="100" step="0.05">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label>{{ __('Rotation') }}:</label>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][rotation][x]" value="" class="form-control form-control-sm" min="-360" max="360" step="1">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][rotation][y]" value="" class="form-control form-control-sm" min="-360" max="360" step="1">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][rotation][z]" value="" class="form-control form-control-sm" min="-360" max="360" step="1">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <label>{{ __('Opacity') }}:</label>
          </div>
          <div class="col-md-9">
            <div class="form-group ml-3">
              <input type="range" name="entity[opacity]" class="form-control-range" value="" min="0" max="1" step="0.1">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 chromakey">
            <div class="form-group">
              <div class="form-check">
                <input type="hidden" name="entity[components][chromakey]" value="0">
                <input class="form-check-input" type="checkbox" name="entity[components][chromakey]" value="1" id="chromakey">
                <label class="form-check-label" for="chromakey">
                  {{ __('It is a chroma key video') }}
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="row text-right">
          <div class="col-md-12 mt-1">
            <a href="#" class="delete-entity">
              <i class="far fa-trash-alt text-danger"></i>    
            </a>
          </div>
        </div>
      </form>
  </details>


  {{-- a-gltf-model form matrix (entities) --}}
  <details class="mb-2 entity entity-form a-gltf-model" hidden>
    <summary><small>{{ __('New Entity') }}</small></summary>
    <form class="mt-2">
      <input type="hidden" name="id" value="">
      <input type="hidden" name="type" value="">
      <input type="hidden" name="marker_id" value="">
      <div class="row">
          <div class="col-md-4">
            <label>{{ __('Name') }}:</label>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" name="name" value="" class="form-control form-control-sm" autocomplete="off">
            </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
            <label>{{ __('Position') }}:</label>
          </div>
      </div>
      <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][position][x]" value="" class="form-control form-control-sm" min="-100" max="100" step="0.05">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][position][y]" value="" class="form-control form-control-sm" min="-100" max="100" step="0.05">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][position][z]" value="" class="form-control form-control-sm" min="-100" max="100" step="0.05">
            </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
            <label>{{ __('Rotation') }}:</label>
          </div>
      </div>
      <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][rotation][x]" value="" class="form-control form-control-sm" min="-360" max="360" step="1">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][rotation][y]" value="" class="form-control form-control-sm" min="-360" max="360" step="1">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][rotation][z]" value="" class="form-control form-control-sm" min="-360" max="360" step="1">
            </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
            <label>{{ __('Scale') }}:</label>
          </div>
      </div>
      <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][scale][x]" value="" class="form-control form-control-sm" min="0.01" max="1000" step="0.01">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][scale][y]" value="" class="form-control form-control-sm" min="0.01" max="1000" step="0.01">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <input type="number" name="entity[components][scale][z]" value="" class="form-control form-control-sm" min="0.01" max="1000" step="0.01">
            </div>
          </div>
      </div>
      <div class="row text-right">
        <div class="col-md-12 mt-1">
          <a href="#" class="delete-entity">
            <i class="far fa-trash-alt text-danger"></i>    
          </a>
        </div>
      </div>
    </form>
  </details>
  

  {{-- a-sound form matrix (entities) --}}

  <details class="mb-2 entity entity-form a-sound" hidden>
    <summary><small>{{ __('New Entity') }}</small></summary>
    <form class="mt-2">
      <input type="hidden" name="id" value="">
      <input type="hidden" name="type" value="">
      <input type="hidden" name="marker_id" value="">
      <input type="hidden" name="entity[autoplay]" value="false">
      <input type="hidden" name="entity[on]" value="null">
      <input type="hidden" name="entity[volume]" value="1">
      <div class="row">
          <div class="col-md-4">
            <label>{{ __('Name') }}:</label>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" name="name" value="" class="form-control form-control-sm" autocomplete="off">
            </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
            <div class="form-check">
              <input type="hidden" name="entity[loop]" value="false">
              <input class="form-check-input" type="checkbox" name="entity[loop]" value="true" id="inputSoundLoop">
              <label class="form-check-label" for="soundLoop">{{ __('Enable loop') }}</label>
            </div>
          </div>
      </div>
      <div class="row text-right">
        <div class="col-md-12 mt-1">
          <a href="#" class="delete-entity">
            <i class="far fa-trash-alt text-danger"></i>    
          </a>
        </div>
      </div>
    </form>
  </details>

</div>


{{-- Modal - Choose Extension --}}

<div class="modal fade" id="chooseExtensionModal" tabindex="-1" aria-labelledby="chooseExtensionModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title" id="addIconModalLabel">{{ __('Choose an Extension') }}</h6>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <table class="table-borderless table-striped" id="extensionItems">
                  <tr class="extension-item" data-type="ContactBar">
                      <td class="display-3 holograma-color pr-3"><i class="fas fa-share-alt-square"></i></td>
                      <td>
                          <h6 class="holograma-color">{{ __('Contact Bar') }}</h6>
                          <p>{{ __('Contact bar that displays your social media icons during the scene.') }}</p>
                      </td>
                      <td class="align-middle"><button class="btn btn-sm holograma-btn btnAddExtension" data-dismiss="modal">{{ __('Add') }}</button></td>
                  </tr>
                  <tr class="extension-item" data-type="ButtonCallToAction">
                      <td class="display-3 holograma-color pr-3"><i class="fas fa-exclamation"></i></td>
                      <td>
                          <h6 class="holograma-color">{{ __('Call to Action Button') }}</h6>
                          <p>{{ __('Displays a call to action button at the bottom of the screen.') }}</p>
                      </td>
                      <td class="align-middle"><button class="btn btn-sm holograma-btn btnAddExtension" data-dismiss="modal">{{ __('Add') }}</button></td>
                  </tr>
              </table>
          </div>
      </div>
  </div>
</div>



<details class="extension border p-2 mb-2 d-none" data-type="ContactBar">
  <summary class="holograma-color"><i class="fas fa-share-alt-square"></i> <small>{{__('Contact Bar')}}</small></summary>
  <div class="mt-2">
      <p><small>{{ __('Enter the address for the contacts you want to add.') }}</small></p>
      <form>
          <div class="form-group">
              <label><i class="fas fa-phone-square"></i> <small>{{ __('Phone Number') }}</small></label>
              <input type="text" name="phone_number"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-whatsapp-square"></i> <small>{{ __('WhatsApp Number') }}</small></label>
              <input type="text" name="whatsapp_number"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-telegram"></i> <small>{{ __('Telegram') }}</small></label>
              <input type="url" name="telegram_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-facebook-messenger"></i> <small>{{ __('Menssenger') }}</small></label>
              <input type="url" name="messenger_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-facebook-square"></i> <small>{{ __('Facebook') }}</small></label>
              <input type="url" name="facebook_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-instagram"></i> <small>{{ __('Instagram') }}</small></label>
              <input type="url" name="instagram_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-twitter-square"></i> <small>{{ __('Twitter') }}</small></label>
              <input type="url" name="twitter_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-github-square"></i> <small>{{ __('Github') }}</small></label>
              <input type="url" name="github_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-linkedin"></i> <small>{{ __('LinkedIn') }}</small></label>
              <input type="url" name="linkedin_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fab fa-youtube-square"></i> <small>{{ __('Youtube') }}</small></label>
              <input type="url" name="youtube_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fas fa-globe"></i> <small>{{ __('Site') }}</small></label>
              <input type="url" name="site_url"  maxlength="500" class="form-control form-control-sm">
          </div>
          <div class="form-group">
              <label><i class="fas fa-at"></i> <small>{{ __('E-mail') }}</small></label>
              <input type="email" name="email"  maxlength="500" class="form-control form-control-sm">
          </div>
          <input type="hidden" name="extension_id">
      </form>
      <div class="text-right">
          <button class="btn btn-sm delete-extension"><i class="fas fa-trash-alt text-danger"></i></button>
      </div>
  </div>
</details>

<details class="extension border p-2 mb-2 d-none" data-type="ButtonCallToAction">
  <summary class="holograma-color"><i class="fas fa-exclamation"></i> <small>{{__('Call to Action Button')}}</small></summary>
  <div class="mt-3">
      <form>
          <div class="form-group">
              <button type="button" class="btn btn-block btn-primary border-0 btn-call-to-action">{{ __('Action') }}</button>
          </div>
          <div class="form-group">
              <label>
                  <small>{{ __('Type the button text.') }}</small>
              </label>
              <input type="text" name="button_text"  maxlength="255" class="form-control form-control-sm">
          </div>
          <div class="form-group row">
              <div class="col-8">
                  <label><small>{{__('Background color')}}</small></label>
              </div>
              <div class="col-4">
                  <input type="text" name="button_background" data-choose="{{ __('Choose') }}" data-cancel="{{ __('Cancel') }}" class="form-control form-control-sm">
              </div>
          </div>
          <div class="form-group row">
              <div class="col-8">
                  <label><small>{{__('Text color')}}</small></label>
              </div>
              <div class="col-4">
                  <input type="text" name="button_textcolor" data-choose="{{ __('Choose') }}" data-cancel="{{ __('Cancel') }}" class="form-control form-control-sm">
              </div>
          </div>
          <div class="form-group">
              <label>
                  <small>{{ __('Enter button link.') }}</small>
              </label>
              <input type="text" name="button_link"  maxlength="255" class="form-control form-control-sm">
          </div>
          <input type="hidden" name="extension_id">
      </form>
      <div class="text-right">
          <button class="btn btn-sm delete-extension"><i class="fas fa-trash-alt text-danger"></i></button>
      </div>
  </div>
</details>

@endsection