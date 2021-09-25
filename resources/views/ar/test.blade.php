@extends('layouts.ar')
@section('title', __('Augmented Reality'))

@section('content')
  @php
    $cameraParametersUrl = asset('files/camera/camera_para.dat');
  @endphp
  <a-scene 
    vr-mode-ui="enabled: false"
    embedded 
    arjs='debugUIEnabled:false; sourceType:webcam; detectionMode: mono_and_matrix; matrixCodeType: 3x3; cameraParametersUrl: {{$cameraParametersUrl}}; patternRatio: 0.9; maxDetectionRate: 60;'
    renderer="logarithmicDepthBuffer: true; precision: medium;">
    <a-marker type="pattern" url="{{ $custommarker->pattern }}" smooth="true" smoothCount="5" smoothTolerance="0.01" smoothThreshold="2">
      <a-plane 
        position="0 0.2 0"
        rotation="-90 0 0"
        width="1"
        height="1"
        color="green">
      </a-plane>
    </a-marker>

    <a-entity camera></a-entity>
  </a-scene>

@endsection