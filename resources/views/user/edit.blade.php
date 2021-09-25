@extends('layouts.dashboard')
@section('title', __('Profile'))

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user"></i> {{ __('Profile') }}
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

@if (session('err'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('err') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form action="{{ url('/users/edit', ['user' => $user->id]) }}" method="POST" id="profile-form">
    @csrf
    @method('PUT')

    <div class="form-group row">
        <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Name') }}</label>
        <div class="col-md-5">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>


    <div class="form-group row">
        <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
        <div class="col-md-5">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required autocomplete="email">

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    @if($role == 'admin')
        <div class="form-group row">
            <label for="role" class="col-md-3 col-form-label text-md-right">{{ __('Status') }}</label>
            <div class="col-md-5">
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    @if ($user->status == 'inactive')
                        <option value="0" {{ $user->status == 'inactive'? 'selected':'' }}>{{ __('Not verified') }}</option>
                    @else
                        <option value="1" {{ $user->status == 'active'? 'selected':'' }}>{{ __('Active') }}</option>
                        <option value="2" {{ $user->status == 'blocked'? 'selected':'' }}>{{ __('Blocked') }}</option>
                    @endif                    
                </select>
                @error('role')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="role" class="col-md-3 col-form-label text-md-right">{{ __('Role') }}</label>
            <div class="col-md-5">
                <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="">{{ __('Select a role') }}</option>
                    <option value="1" {{ $user->role == 'editor'? 'selected':'' }}>{{ __('Editor') }}</option>
                    <option value="0" {{ $user->role == 'admin'? 'selected':'' }}>{{ __('Admin') }}</option>
                </select>
                @error('role')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    @endif

    <div class="form-group row">
        <label for="newpassword" class="col-md-3 col-form-label text-md-right">{{ __('New Password') }}</label>
        <div class="col-md-5">
            <input id="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" autocomplete="newpassword">
            @error('newpassword')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label for="newpassword-confirm" class="col-md-3 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>
        <div class="col-md-5">
            <input id="newpassword-confirm" type="password" class="form-control @error('newpassword_confirmation') is-invalid @enderror" name="newpassword_confirmation" autocomplete="newpassword">
            @error('newpassword_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

        
    <div class="form-group row">
        <label for="password" class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>
        <div class="col-md-5">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-5 offset-md-3">
            @if ($user->status == 'inactive')
            <a href="{{ url('users/email/resend', ['user' => $user->id]) }}" class="btn btn-secondary">
                    {{ __('Resend Verification Link') }}
                </a>
            @endif
            <button type="submit" class="btn holograma-btn">
                {{ __('Save Changes') }}
            </button>
        </div>
    </div>


</form>
@endsection
