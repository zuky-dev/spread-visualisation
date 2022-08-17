@extends('layouts.app')

@section('title', 'Orderbook')

@push('head')
    @vite('resources/css/app.css')
@endpush

@push('scripts')
    @vite('resources/js/app.js')
@endpush

@section('content')
    <div id="app"></div>
@endsection