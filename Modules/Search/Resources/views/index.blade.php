@extends('layouts.layout')
@section('content')
    <div class="container">
        {!! $page->description !!}
        <search source="{{ route('search-result') }}" query="{{ request()->get('q') }}" by="{{ request()->get('by') }}"></search>
    </div>
    {!! $page->description_footer !!}
@endsection
