@extends('layouts.app',['pageTitle' => '401'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>401</li>
</ul>

@endsection

@section('content')
<div class="errortitle">
    <h4 class="animate0 fadeInUp">You Have no rights to access this page</h4>
    <span class="animate1 bounceIn">4</span>
    <span class="animate2 bounceIn">0</span>
    <span class="animate3 bounceIn">1</span>
    <div class="errorbtns animate4 fadeInUp">
        <a onclick="history.back()" class="btn btn-primary btn-large">Go to Previous Page</a>
    
        @if(auth()->user()->id == 1)
            <a href="{{ route('dashboard') }}" class="btn btn-large">Go to Dashboard</a>
        @endif
    </div>
</div>
@endsection
