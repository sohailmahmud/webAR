@extends('layouts.dashboard')
@section('title', __('Users'))

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-users"></i> {{ __('Users') }}
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
    'inactive' => [__('Not verified'), 'warning'],
    'active' => [__('Active'), 'success'],
    'blocked' => [__('Blocked'), 'danger'], 
  ]; 
?>

<div class="row mb-3">
  <div class="col">
    <a href="{{ url('users/add') }}" class="btn holograma-btn"><i class="fas fa-user"></i> {{ __('Add User') }}</a>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <caption>{{ __('Page :page of :pages. Total of :total users.', ['page' => $users->currentPage(), 'pages' => $users->lastPage(), 'total' => $users->total()]) }}</caption>
    <thead class="thead-light">
      <tr>
        <th scope="col">#ID</th>
        <th scope="col">{{ __('Name') }}</th>
        <th scope="col">{{ __('E-mail') }}</th>
        <th scope="col">{{ __('Role') }}</th>
        <th scope="col">{{ __('Status') }}</th>
        <th scope="col">{{ __('Registered') }}</th>
        <th scope="col">{{ __('Action') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($users as $i => $user)
        <tr>
          <th scope="row">{{ $user->id }}</th>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ ucfirst($user->role) }}</td>
          <td><span class="badge badge-{{ $status[$user->status][1] }}">{{ $status[$user->status][0] }}</span></td>
          <td>{{ substr($user->created_at, 0, 10) }}</td>
          <td>
              <ul class="list-inline">
                <li class="list-inline-item">
                  <a href="{{ url('users/edit', ['user' => $user->id]) }}" class="btn btn-link text-primary btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                  </a>
                </li>
                <li class="list-inline-item">
                  <form 
                    id="form-delete-user"
                    method="POST" 
                    action="{{ url('/users', ['user' =>  $user->id ]) }}"
                    data-message="{{ __('Do you want to delete the user?') }}">
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

 {{ $users->onEachSide(5)->links() }}

@endsection