<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body style="overflow-x:hidden;">
    <div id="app" style="display:none">
        <topo v-cloak titulo="{{ config('app.name', 'Laravel') }}" url="{{ url('/') }}">
            <!-- Authentication Links -->
                        @guest
                            <li><a style="color: white;size:40px;" href="{{ route('login') }}">Login</a></li>
                            <li><a style="color: white;size:40px;" href="{{ route('register') }}">Register</a></li>
                        @else
                             <li><a style="color: white;size:40px; font-weight: bold;" href="{{route('admin')}}">Menu</span></a></li> 
                             <li><a style="color: white;size:40px;" href="index.php"><span class="glyphicon glyphicon-home"></span></a></li>                        
                             <li><a style="color: white;size:40px;" href="cadastrar_horario.php"><span class="glyphicon glyphicon-envelope"></span></a></li>
                             <li><a style="color: white;size:40px;" href="cadastrar_turma.php"><span class="glyphicon glyphicon-bell"></span></a></li>
                             
                             <li class="dropdown">
                                <a style="color: white;font-weight: bold;" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @can('professor')
                                    <li>
                                        <a href="{{route('admin')}}">Admin</a>
                                    </li>
                                    @endcan
                                    <li>
                                        <a  href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest        
        </topo>



        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
