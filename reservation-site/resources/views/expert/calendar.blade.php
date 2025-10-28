@extends('adminlte::page')

@section('title', 'My Calendar')

@section('content_header')
    <h1>My Calendar</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
    @vite(['resources/js/app.js'])
@stop
