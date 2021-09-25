@extends('layouts.ar')
@section('title', __('Augmented Reality'))

@section('content')
  @php
    $cameraParametersUrl = asset('files/camera/camera_para.dat');
  @endphp
  <a-scene
    vr-mode-ui="enabled: false"
    embedded 
    arjs='debugUIEnabled:false; sourceType:webcam; trackingMethod: best; cameraParametersUrl: {{$cameraParametersUrl}}; patternRatio: 0.9; maxDetectionRate: 60;'
    renderer="logarithmicDepthBuffer: true; precision: medium;">
    {{-- scene is public (not draft or archived) --}}
    @if ($scene->status == 1)
      <a-assets>
        @foreach ($entities as $entity)
          @php
            $assetType = $entity->props->asset->type; // img, video, audio, model
          @endphp
          @switch($assetType)
            @case('img')
              <img id="asset-{{ $entity->id }}" src="{{ $entity->props->entity->src }}">
              @break
            @case('video')
              <video id="asset-{{ $entity->id }}" src="{{ $entity->props->entity->src }}" class="media"></video>
              @break
            @case('audio')
              <audio id="asset-{{ $entity->id }}" src="{{ $entity->props->entity->src }}" class="media"></audio>
              @break
            @case('model')
              <a-asset-item id="asset-{{ $entity->id }}" src="{{ $entity->props->entity->src }}"></a-asset-item>
              @break
          @endswitch  
        @endforeach
      </a-assets>
    @endif
    <a-marker emitevents="true" markerhandler type="pattern" url="{{ $marker->pattern }}" smooth="true" smoothCount="5" smoothTolerance="0.01" smoothThreshold="2">
      @php
        $attr = ''; // Entity attribute
      @endphp     
      {{-- scene is public (not draft or archived) --}}
      @if ($scene->status == 1)
        @foreach ($entities as $entity)
          @php
            $type = $entity->type; // a-image, a-video, a-sound, a-gltf-model 
            $props = $entity->props->entity;
            if($type == 'a-gltf-model') {
              $attr .= ' animation-mixer';
            }
            if($type == 'a-image' && $entity->props->asset->ext == 'gif') {
              $attr .= ' shader="gif"';
            }
            if($type == 'a-video' && isset($entity->props->entity->chromakey) && $entity->props->entity->chromakey == 1) {
              $attr .= " shader=\"chromakey\" material=\"color: 0.1 0.9 0.2;\"";
            }
          @endphp
          @foreach ($props as $prop => $v)
            @switch($prop)
                @case('position')
                @case('rotation')
                @case('scale')
                  @php
                    $x = $v->x; 
                    $y = $v->y; 
                    $z = $v->z;
                    if($prop == 'position') {
                      $y += 1.5;            // resources/js/scene/main.js line ~950
                      $y -= 0.75;  // resources/views/scene/edit.blade.php line ~75
                    }
                    $attr .= " $prop=\"$x $y $z\"";
                  @endphp
                  @break
                @case('src')
                  @php
                    $src = '#asset-' . $entity->id;
                    if($type == 'a-sound') {
                      $src = 'src: ' . $src;
                    } 
                    $attr .= " src=\"$src\"";   
                  @endphp
                  @break
                @default
                  @php
                    $attr .= " $prop=\"$v\"";
                  @endphp
                  @break          
            @endswitch
          @endforeach 
          @php
            $attr .= ' id="entity-obj-' .$entity->id. '"';
            echo "<$type $attr></$type>";   
            $attr = '';
          @endphp
        @endforeach
      @else
        <a-text
          id="text-not-public" 
          visible="true"
          value="{{ __('This scene is not public.') }}" 
          width="4"
          height="4"
          align="center"
          color="yellow"
          position="0 0.25 0"
          rotation="-45 0 0">
        </a-text>
      @endif
    </a-marker>
    <a-light type="ambient" color="#fff"></a-light>
    <a-light type="directional" color="#fff" intensity="0.3" position="-0.5 1 1"></a-light>
    <a-entity camera></a-entity>
  </a-scene>

  {{-- ====================== HTML ELEMENTS ======================= --}}



  {{-- Loading --}}
  <div class="arjs-loader">
    <div>
      <img src="{{ asset($LOGO_FILE) }}" class="d-block mx-auto">
      <p>{{ __('Loading, wait...') }}</p>
      <p>{{ __('Point the camera at the QR Code') }}</p>
    </div>
  </div>

  {{-- Button Play/Pause --}}
  <div class="btn-playpause-container">
    <button id="btn-play-pause" class="btn btn-playpause">
      <i class="fas fa-play"></i>
      <i class="fas fa-pause"></i>
    </button>
  </div>



 {{-- ====================== EXTENSIONS ======================= --}}
{{--  
      - ContactBar
      - ButtonCallToAction 
--}}

@foreach ($extensions as $extension)

  @if ($extension->type == 'ContactBar')
    {{-- 
      props:
        - phone_number
        - whatsapp_number 
        - telegram_url
        - messenger_url
        - facebook_url
        - instagram_url
        - twitter_url
        - github_url
        - linkedin_url
        - site_url
        - email 
      --}}

      <div id="contactBar">
        @if ($extension->props['phone_number'])
          <div class="text-center">
            <a href="tel:{{$extension->props['phone_number']}}"><i class="fas fa-phone-square phone"></i></a>
          </div> 
        @endif
        @if ($extension->props['whatsapp_number'])
          <div class="text-center">
            <a href="https://api.whatsapp.com/send?phone={{$extension->props['whatsapp_number']}}"><i class="fab fa-whatsapp-square whatsapp"></i></a>
          </div> 
        @endif
        @if ($extension->props['telegram_url'])
          <div class="text-center">
            <a href="{{$extension->props['telegram_url']}}"><i class="fab fa-telegram telegram"></i></a>
          </div> 
        @endif
        @if ($extension->props['messenger_url'])
          <div class="text-center">
            <a href="{{$extension->props['messenger_url']}}"><i class="fab fa-facebook-messenger messenger"></i></a>
          </div> 
        @endif
        @if ($extension->props['facebook_url'])
          <div class="text-center">
            <a href="{{$extension->props['facebook_url']}}"><i class="fab fa-facebook-square facebook"></i></a>
          </div> 
        @endif
        @if ($extension->props['instagram_url'])
          <div class="text-center">
            <a href="{{$extension->props['instagram_url']}}"><i class="fab fa-instagram instagram"></i></a>
          </div> 
        @endif
        @if ($extension->props['twitter_url'])
          <div class="text-center">
            <a href="{{$extension->props['twitter_url']}}"><i class="fab fa-twitter-square twitter"></i></a>
          </div> 
        @endif
        @if ($extension->props['github_url'])
          <div class="text-center">
            <a href="{{$extension->props['github_url']}}"><i class="fab fa-github-square github"></i></a>
          </div> 
        @endif
        @if ($extension->props['linkedin_url'])
          <div class="text-center">
            <a href="{{$extension->props['linkedin_url']}}"><i class="fab fa-linkedin linkedin"></i></a>
          </div> 
        @endif
        @if ($extension->props['youtube_url'])
          <div class="text-center">
            <a href="{{$extension->props['youtube_url']}}"><i class="fab fa-youtube-square linkedin"></i></a>
          </div> 
        @endif
        @if ($extension->props['site_url'])
          <div class="text-center">
            <a href="{{$extension->props['site_url']}}"><i class="fas fa-globe site"></i></a>
          </div> 
        @endif
        @if ($extension->props['email'])
          <div class="text-center">
            <a href="mailto:{{$extension->props['email']}}"><i class="fas fa-at email"></i></a>
          </div> 
        @endif
      </div>
      
  @endif

  @if ($extension->type == 'ButtonCallToAction')
    {{-- 
      props:
        - button_text
        - button_background
        - button_textcolor
        - button_link 
    --}}
    @php
      $ButtonCallToActionStyle = "background-color: {$extension->props['button_background']};"; 
      $ButtonCallToActionStyle .= "color: {$extension->props['button_textcolor']};";
    @endphp
    <a 
      class="btn btn-lg btn-block button-call-to-action" 
      href="{{ $extension->props['button_link'] }}">
        {{ $extension->props['button_text'] }}
    </a>
  @endif

@endforeach


@push('head_css')

  <style>
  .button-call-to-action {
    position: fixed;
    bottom: 0;
    left: 0;
    border: none;
    border-radius: 0;
    {{ $ButtonCallToActionStyle?? '' }}
  }
</style>

@endpush


@endsection
