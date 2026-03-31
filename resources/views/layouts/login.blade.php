<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meat name="robots" content="noindex,nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/icons/ravilogowhite.jpg') }}">
{{-- <title>Bhumi Irrigation Systems</title> --}}
<title>Bhumi Polymers Pvt. Ltd.</title>
<link rel="stylesheet" href="{{ asset('css/style.default.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/style.navyblue.css') }}">
<script type="text/javascript" src="{{ asset('js/jquery-1.9.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

</head>

<body class="loginpage">

<!-- Login form -->
    @yield('content')
<!-- // -->

<div class="loginfooter">
     <div class="logo-footer animate0 bounceIn"><img src="{{ asset('images/icons/cbs_webtech.png') }}" alt="company logo" height="99px" width="145px"/></div> 
    <p>&copy; 2024. Bhumi Polymers Pvt. Ltd. &nbsp;All Rights Reserved.</p>
</div>

</body>
<!-- include scripts -->
@hasSection('scripts')
    @yield('scripts')
@endif
<!-- // -->
</html>