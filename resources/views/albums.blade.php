@extends('layouts.app')

@section('content')
<div class="album-menu">
    <div class="album-menu-title">Albums</div>
    @foreach($tags->chunk(4) as $chunk)
        <div class="album-row">
            @foreach($chunk as $album)
                <div class="col-md-3 col-sm-6">
                    <div class="album">
                        <a href="{{ route('show', ['tag' => $album->tag ]) }}">
                        <img src="{{ asset('/storage'.$album->photos->first()->path) }}" alt="Album image">
                        <a href="{{ route('show', ['tag' => $album->tag ]) }}">{{ $album->tag }}</a>
                        <div class="album-photo-count"><i class="fas fa-camera"></i>{{ $album->photos->count() }}</div></a>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection
