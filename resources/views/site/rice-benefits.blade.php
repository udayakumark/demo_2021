@extends('layouts.site-layout')
@section('title', 'Sakthi Rice | Benefits of Rice')
@section('content')
<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Benefits of Rice</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->

<!--====== About Part Start ======-->
<section class="about-page pt-20">
    <div class="container-fluid custom-container">
        @if(count($RiceBenefits)>0)
        @foreach($RiceBenefits as $benefit)
        <div class="row">
            <div class="col-lg-6">
                <div class="about-image mt-30">
                    <img src="{{ asset('public/'.$benefit['image']) }}" alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content mt-30">
                    <h2 class="title">{{ $benefit['title'] }}</h2>
                    <p>{{ $benefit['description'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</section>
<!--====== About Part Ends ======-->
@endsection