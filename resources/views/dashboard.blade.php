{{-- @extends('layouts.app',['pageTitle' => 'Bhumi Irrigation Systems','isFront' => 'true']) --}}
@extends('layouts.app',['pageTitle' => 'Bhumi Polymers Pvt. Ltd.','isFront' => 'true'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Dashboard</li>
</ul>

<div class="pageheader">
    <div class="pageicon"><span class="iconfa-laptop"></span></div>
    <div class="pagetitle">
        <h1>Dashboard</h1>
    </div>
</div><!--pageheader-->
@endsection

@section('content')
    <div class="blank-container"><div class="welcome-text"><h1 class="name_as_is">Welcome, {{ Auth::user()->user_name }}</h1></div></div>
@endsection