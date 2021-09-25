<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="app-url" content="{{ env('APP_URL') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Font Awesome Free 5.8.2 and Google Fonts -->
    <link href="{{ asset('css/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" type="text/css">

    <!-- Bootstrap v4.3.1 + SB Admin 2 v4.0.5, Dynamic CSS and Custom Style-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/spectrum-colorpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('files/dynamic.css') . '?' . time()}}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    @stack('scripts_head')
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion holograma-backgroundcolor" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/dashboard') }}">
                <div class="sidebar-brand-icon">
                    <img  src="{{ $ICON_FILE }}" width="40" height="40" class="img-fluid" />
                </div>
                <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Laravel') }}</div>
            </a>
        
            <!-- Divider -->
            <hr class="sidebar-divider">
        
            <!-- Heading -->
            <div class="sidebar-heading">
                {{ __('Editor Menu') }}
            </div>
         
            <!-- Nav Item - Create Scene -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('scene/create') }}">
                    <i class="fas fa-cube"></i>
                    <span>{{ __('Create Scene') }}</span>
                </a>
            </li>
        
            <!-- Nav Item - My Scenes -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/myscenes') }}">
                    <i class="fas fa-cubes"></i>
                    <span>{{ __('My Scenes') }}</span>
                </a>
            </li>

            <!-- Nav Item - Custom Markers -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/mycustommarkers') }}">
                    <i class="fas fa-th-large"></i>
                    <span>{{ __('My Custom Markers') }}</span>
                </a>
            </li>

                        <!-- Nav Item - Profile -->
            <li class="nav-item">
                <a class="nav-link" href="{{ url('users/edit', ['user' => Auth::id()]) }}">
                    <i class="fas fa-user"></i>
                    <span>{{ __('Profile') }}</span>
                </a>
            </li>
    
        
            @if(Auth::user()->role == 'admin')

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    {{ __('Admin Menu') }}
                </div>
            
            
                <!-- Nav Item - Scenes -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/scenes') }}">
                        <i class="fas fa-cubes"></i>
                        <span>{{ __('Scenes') }}</span>
                    </a>
                </li>

                <!-- Nav Item - Custom Markers -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/custommarkers') }}">
                        <i class="fas fa-th-large"></i>
                        <span>{{ __('Custom Markers') }}</span>
                    </a>
                </li>

                <!-- Nav Item - Users -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/users') }}">
                        <i class="fas fa-users"></i>
                        <span>{{ __('Users') }}</span>
                    </a>
                </li>

                <!-- Nav Item - Settings -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/configs') }}">
                        <i class="fas fa-fw fa-cog"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                </li>

            @endif

            <!-- Divider -->
            <hr class="sidebar-divider">
            
            <!-- Nav Item - Logout -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </a>
            </li>
    
        </ul>
        <!-- End of Sidebar -->
        

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content" @if(isset($dashboard_home)) class="dashboard_bg" @endif>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <main>
                        <div id="app" class="mt-3">
                            @yield('content')
                        </div>
                    </main>               
            
                </div>
                <!-- End Page Content -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>{{ config('app.name', 'Laravel') }} - {{ __('Augmented Reality Builder') }}</span>
                </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">{{ __('Select Logout below if you are ready to end your current session.') }}</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <a id="btn-logout"
                        class="btn btn-primary" 
                        href="{{ route('logout') }}">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Message Modal --}}
    <div class="modal fade" id="modalMessage" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Message') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('OK') }}</button>
                </div>
            </div>
        </div>
    </div>
    
    @stack('scripts_bottom1')
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
