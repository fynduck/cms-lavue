@extends('layouts.layout')

@section('content')
    <div class="container">
        <h1 class="title_form">{{ $page->title }}</h1>
    </div>
    {!! $page->description !!}
@endsection

