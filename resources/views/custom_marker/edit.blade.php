@extends('layouts.dashboard')
@section('title', __('Create Custom Marker'))

@section('content')

@push('scripts_bottom1')
  <script src="{{ asset('js/threex-arpatternfile.js') }}"></script>
@endpush

{{-- Wrapper Custom Marker Page --}}
<div id="create-custom-marker">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-fw fa-cog"></i> {{ __('Create Custom Marker') }}
      </h1>
  </div>

  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <form action="{{ url('/custommarkers/edit', ['custommarker' => $custommarker->id]) }}" method="POST" enctype="multipart/form-data" id="create-custom-marker-form">
    @csrf
    @method('PUT')

      <div class="row">
        <div class="col col-md-6">

          <div class="form-group row">
            <label for="thetitle" class="col-sm-4 col-md-4 col-form-label">{{ __('Title') }}</label>
            <div class="col-sm-8 col-md-8"> 
              <input type="text" name="title" value="{{ $custommarker->title }}" class="form-control" id="thetitle" maxlength="100" required>         
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-md-4 col-form-label">{{ __('Image') }}</label>
              <div class="col-sm-8 col-md-8">
                              
                <div class="custom-file">
                  <input type="file" name="custom_marker_file_input" class="custom-file-input" id="custom_marker_file_input" accept="image/png,image/gif,image/jpeg" required>
                  <label class="custom-file-label" for="custom_marker_file_input" data-browse="{{ __('Browse') }}">{{ __('Choose file') }}</label>
                  <small id="icon-help" class="form-text text-muted">
                    {{ __('Select a square image.') }}
                  </small> 
                </div>

                <label class="col-form-label" id="custom_marker_image_container">
                  @if ($custommarker->image)
                    <img src="{{ $custommarker->image }}" width="400" class="img-thumbnail">
                  @endif
                </label>

                @if ($custommarker->pattern)
                  <div class="alert alert-warning" role="alert">
                    <p><strong>{{ __('Validate the created marker!') }}</strong> {{ __('Go to the url below from your smartphone, share the camera and point to the created marker. If it is valid, the green color will appear.') }}</p>
                    <hr>
                    <p class="mb-0">{{ $custommarker->validation_url }}</p>
                  </div>
                @endif

              </div>
          </div>

        </div>

        <div class="col col-md-4">
          <div class="alert alert-info" role="alert">
            <h5 class="alert-heading"> {{ __('Marker Creation Rules') }}</h5>
            <hr>
            <p>1. {{ __('Do not use complex images') }};</p>
            <p>2. {{ __('The image must be square') }};</p>
            <p>3. {{ __('The image must be rotationally asymmetric') }};</p>
            <p>4. {{ __('The foreground color of the image should contrast with the background color') }};</p>
            <p>5. {{ __('The background color of the image should contrast with the black border') }};</p>
            <p>6. {{ __('After generating the marker, you must validate it') }}.</p>
          </div>
        </div>

      </div>

      <input type="text" name="custom_marker_image" id="custom_marker_image" value="{{ $custommarker->image }}" hidden>
      <input type="text" name="custom_marker_image_thumb" id="custom_marker_image_thumb" value="{{ $custommarker->thumb }}" hidden>
      <textarea name="custom_marker_pattern" id="custom_marker_pattern" value="{{ $custommarker->pattern }}" hidden></textarea>

      <div class="row">
        <div class="col col-md-6">
          <div class="row">
            <div class="col col-md-8 offset-md-4 col-sm-8 offset-sm-4">
              <input type="submit" class="btn btn-primary my-4 ml-5 mx-auto" value="{{ __('Save Changes') }}" >
            </div>
          </div>   
        </div>
      </div>

  </form>

</div>

@endsection
