<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>

    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
          
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css"/>

    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css"
          integrity="sha512-8vq2g5nHE062j3xor4XxPeZiPjmRDh6wlufQlfC6pdQ/9urJkU07NM0tEREeymP++NczacJ/Q59ul+/K2eYvcg=="
          crossorigin="anonymous"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
          integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
          crossorigin="anonymous"/>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
          integrity="sha512-aEe/ZxePawj0+G2R+AaIxgrQuKT68I28qh+wgLrcAJOz3rxCP+TwrK5SPN+E5I+1IQjNtcfvb96HDagwrKRdBw=="
          crossorigin="anonymous"/>

    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->

    <style>
        .divider {
            width: 100%;
            margin: 10px auto;
            height: 1px;
            background-color: #dedede;
        }

        .ico-tab {
            margin-right: 15px;
        }

        .ico-tab-mini {
            margin-right: 4px;
        }

        .badge-lg {
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9em !important;
            margin-right: 25px;
        }

        .bg-disabled {
            background:#878787;
            color: white;
        }

        .radio-group-horizontal {
            border: 1px solid #dcdcdc;
            display: flex;
            padding: 6px;
            border-radius: 3px;
        }

        .radio-group-horizontal input {
            margin-left: 3px;
            margin-right: 3px;
        }

        .radio-group-horizontal label {
            margin-left: 20px;
            margin-right: 20px;
        }
    </style>

    @yield('third_party_stylesheets')

    @stack('page_css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    {{-- <img src="https://boheco1.com/wp-content/uploads/2018/06/boheco-1-1024x1012.png" class="user-image img-circle elevation-2" alt="User Image"> --}}
                    <img src="data:image/jpeg;base64,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"
                         class="user-image img-circle elevation-2" alt="User Image"> 
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        {{-- <img src="https://boheco1.com/wp-content/uploads/2018/06/boheco-1-1024x1012.png" class="user-image img-circle elevation-2" alt="User Image"> --}}
                        <img src="data:image/jpeg;base64,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"
                             class="img-circle elevation-2"
                             alt="User Image"> 
                        <p>
                            {{ Auth::user()->name }}
                            <small>Member since {{ Auth::user()->created_at->format('M. Y') }}</small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="#" class="btn btn-default btn-flat float-right"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Left side column. contains the logo and sidebar -->
@include('layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 3.0.5
        </div>
        <strong>Copyright &copy; 2014-2020 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights
        reserved.
    </footer>
</div>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous"></script> -->
<!-- jQuery -->
<script src="https://adminlte.io/themes/v3/plugins/jquery/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" 
        crossorigin="anonymous"></script>

<script src="https://adminlte.io/themes/v3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>


<!-- AdminLTE App -->
<script src="https://adminlte.io/themes/v3/dist/js/adminlte.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"
        integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg=="
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"
        integrity="sha512-GDey37RZAxFkpFeJorEUwNoIbkTwsyC736KNSYucu1WJWFK9qTdzYub8ATxktr6Dwke7nbFaioypzbDOQykoRg=="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous"></script>
        
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js" integrity="sha512-J+763o/bd3r9iW+gFEqTaeyi+uAphmzkE/zU8FxY6iAvD3nQKXa+ZAWkBI9QS9QkYEKddQoiy0I5GDxKf/ORBA==" crossorigin="anonymous"></script>

<script src="https://cdn.lordicon.com/libs/mssddfmo/lord-icon-2.1.0.js"></script>

<script>
    $(function () {
        bsCustomFileInput.init();
    });
    
    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

        /** add active class and stay opened when selected */
    var url = window.location;

    // for sidebar menu entirely but not cover treeview
    $('ul.nav-sidebar a').filter(function() {
        return this.href == url;
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

    // APPLICATION JS
    $(document).ready(function() {
        /**
         * MEMBERSHIP RELATED SCRIPTS
         */

         /**
          * Initialize Juridical fields
          */
        if ($('#MembershipType option:selected').text() == 'Juridical') {
            $('#OrgranizationNameModule').show();
            $('#NonJuridicals').hide();
        } else {
            $('#OrgranizationNameModule').hide();
            $('#NonJuridicals').show();
        }

        $('#MembershipType').on('change', function() {
            if ($('#MembershipType option:selected').text() == 'Juridical') {
                $('#OrgranizationNameModule').show();
                $('#NonJuridicals').hide();
            } else {
                $('#OrgranizationNameModule').hide();
                $('#NonJuridicals').show();
            }
        });

        /**
         * TOWN CHANGE
         */
        fetchBarangayFromTown($('#Town').val(), $('#Def_Brgy').text());

        $('#Town').on('change', function() {
            fetchBarangayFromTown(this.value, $('#Def_Brgy').text());
        });

        /**
         * SERVICE CONNECTION SCRIPTS
         */
        $('#organizationNo').hide();
    });

    /**
     * FUNCTIONS
     */
    function fetchBarangayFromTown(townId, prevValue) {
        $.ajax({
            url : '/barangays/get-barangays-json/' + townId,
            type: "GET",
            dataType : "json",
            success : function(data) {
                $('#Barangay option').remove();
                $.each(data, function(index, element) {
                    $('#Barangay').append("<option value='" + element + "' " + (element==prevValue ? "selected='selected'" : " ") + ">" + index + "</option>");
                });
            },
            error : function(error) {
                // alert(error);
                console.log(error);
            }

        });
    }
</script>

@yield('third_party_scripts')

@stack('page_scripts')
</body>
</html>
