@extends('layouts.in')

@section('body')
    <livewire:camera-grid :list="$list" :enterprises="$enterprises"/>
@endsection
