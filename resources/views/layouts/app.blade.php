<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
        integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
        crossorigin="anonymous" />

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    @yield('third_party_stylesheets')

    @stack('page_css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <script src="{{ mix('js/app.js') }}" defer></script>

    <script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js">
    </script>

    <script>
        // Date renderer for DataTables from cdn.datatables.net/plug-ins/1.10.21/dataRender/datetime.js
        $.fn.dataTable.render.moment = function(from, to, locale) {
            if (arguments.length === 1) {
                locale = 'en';
                to = from;
                from = 'YYYY-MM-DD';
            } else if (arguments.length === 2) {
                locale = 'en';
            }
            return function(d, type, row) {
                if (!d) {
                    return type === 'sort' || type === 'type' ? 0 : d;
                }
                var m = window.moment(d, from, locale, true);
                return m.format(type === 'sort' || type === 'type' ? 'x' : to);
            };
        };
    </script>

    <div class="wrapper">
        <!-- Main Header -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown user-menu d-flex align-items-center">
                    <div class="mx-3">{{ Auth::user()->email }}</div>
                    <a href="#" class="btn btn-default btn-flat float-right"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Sign out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
            </ul>
            </li>
            </ul>
        </nav>

        <!-- Left side column. contains the logo and sidebar -->
        @include('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="container-lg">
                @yield('content')
            </section>
        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.0.5
            </div>
            <strong>Copyright &copy; 2014-2022 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights
            reserved.
        </footer>
    </div>
    @yield('third_party_scripts')

    @stack('page_scripts')
</body>

</html>
