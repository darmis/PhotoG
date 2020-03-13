@extends('layouts.app')

@section('content')
    <div class="image-list-menu">
        <div class="image-list-menu-title">Tag - {{ $tagRequested }} - images</div>
        @if($photos === true)
            <img-grid :tag={{ $tagRequested }}></img-grid>
        @else
            <div style="text-align: center;">No photos found</div>
        @endif
    </div>
@endsection
