@extends('layouts.dashboard')
@section('title', __('My Custom Markers'))

@section('content')

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-th-large"></i> {{ __('Custom Markers') }}
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

  <div class="row mb-3">
    <div class="col">
      <a href="{{ url('/custommarker/create') }}" class="btn holograma-btn"><i class="fas fa-th-large"></i> {{ __('Create Custom Marker') }}</a>
    </div>
  </div>

  <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <caption>{{ __('Page :page of :pages. Total of :total custom markers.', ['page' => $custom_markers->currentPage(), 'pages' => $custom_markers->lastPage(), 'total' => $custom_markers->total()]) }}</caption>
        <thead class="thead-light">
          <tr>
            <th scope="col">#ID</th>
            <th scope="col">{{ __('Image') }}</th>
            <th scope="col" class="title-column">{{ __('Title') }}</th>
            <th scope="col" class="action-column">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($custom_markers as $i => $custommarker)
            <tr>
              <th scope="row" class="text-center">{{ $custommarker->id }}</th>
              <td class="text-center">
                @if($custommarker->thumb)
                  <img src="{{ asset("files/custom_markers/$custommarker->thumb") }}" class="img-thumbnail">
                @endif
              </td>
              <td>{{ $custommarker->title }}</td>
              <td>
                <ul class="list-inline">
                  <li class="list-inline-item">
                    <a href="{{ url('/custommarkers/edit', ['id' => $custommarker->id]) }}" class="btn btn-link text-primary btn-sm">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                  </li>
                  <li class="list-inline-item">
                    <form 
                      id="form-delete-custom-marker"
                      method="POST" 
                      action="{{ url('/custommarkers', ['id' =>  $custommarker->id ]) }}" 
                      data-message="{{ __('Do you want to delete the custom marker?') }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link text-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                    </form>
                  </li>
                </ul>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  {{ $custom_markers->onEachSide(5)->links() }}

@endsection
