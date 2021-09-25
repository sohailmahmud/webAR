@extends('layouts.dashboard')
@section('title', __('My Scenes'))

@section('content')

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-cubes"></i> {{ __('My Scenes') }}
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

  <?php 
  $status = [
    ['warning',  __('Draft')], 
    ['success', __('Published')], 
    ['secondary', __('Archived')]
  ]; 
  ?>

  <div class="row mb-3">
    <div class="col">
      <a href="{{ url('/scene/create') }}" class="btn holograma-btn"><i class="fas fa-cube"></i> {{ __('Create Scene') }}</a>
    </div>
  </div>

  <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <caption>{{ __('Page :page of :pages. Total of :total scenes.', ['page' => $scenes->currentPage(), 'pages' => $scenes->lastPage(), 'total' => $scenes->total()]) }}</caption>
        <thead class="thead-light">
          <tr>
            <th scope="col">#ID</th>
            <th scope="col" class="title-column">{{ __('Title') }}</th>
            <th scope="col">{{ __('Description') }}</th>
            <th scope="col">{{ __('Status') }}</th>
            <th scope="col">{{ __('Editable') }}</th>
            <th scope="col">{{ __('Created') }}</th>
            <th scope="col">{{ __('Published') }}</th>
            <th scope="col" class="action-column">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($scenes as $i => $scene)
            <tr>
              <th scope="row">{{ $scene->id }}</th>
              <td>{{ $scene->title }}</td>
              <td>{{ strlen($scene->description) > 300? substr($scene->description, 0, 300) . ' ...': $scene->description }}</td>
              <td><span class="badge badge-{{ $status[$scene->status][0] }}">{{ $status[$scene->status][1] }}</span></td>
              <td>{{ $scene->editable? __('Yes'): __('No') }}</td>
              <td>{{ substr($scene->created_at, 0, 10) }}</td>
              <td>{{ $scene->published_at? substr($scene->published_at, 0, 10): __('No') }}</td>
              <td>
                <ul class="list-inline">
                  <li class="list-inline-item">
                    <a href="{{ url('/scenes/edit', ['scene' => $scene->id]) }}" class="btn btn-link text-primary btn-sm">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                  </li>
                  <li class="list-inline-item">
                    <form 
                      id="form-delete-scene"
                      method="POST" 
                      action="{{ url('/scenes', ['scene' =>  $scene->id ]) }}" 
                      data-message="{{ __('Do you want to delete the scene?') }}">
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

  {{ $scenes->onEachSide(5)->links() }}

@endsection
