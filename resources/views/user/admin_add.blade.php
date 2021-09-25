@extends('layouts.dashboard')
@section('title', __('Add User'))

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Add User') }}</h1>
</div>
    
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show"" role="alert">
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
    
<form method="POST" action="{{ route('users_add') }}">
    @csrf

    <div class="form-group row">
        <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Name') }}</label>
        <div class="col-md-5">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
    
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
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('name') }}" required autocomplete="email">
            @error('email')
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
                <option value="1" {{ old('role') == '1'? 'selected':'' }}>{{ __('Editor') }}</option>
                <option value="0" {{ old('role') == '0'? 'selected':'' }}>{{ __('Admin') }}</option>
            </select>
            @error('role')
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

    <div class="form-group row">
        <label for="password-confirm" class="col-md-3 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
        <div class="col-md-5">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-5 offset-md-3">
            <button type="submit" class="btn holograma-btn">
                {{ __('Add User') }}
            </button>
        </div>
    </div>

</form>    

@endsection    