@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card" style="width: 18rem;">
                {{--@if(Storage::disk('images')->has($video->image))
                <img src="{{ url('/miniatura/'.$video->image) }}" class="card-img-top" alt="..."/>
                @endif
                --}}
                    <video controls id="video-player">
                    <source src="{{route('fileVideo',['filename' =>$video->video_path])}}"> </source>
                    Tu navegador no es compatible con HTML5
                    </video>
  <div class="card-body">
    <h5 class="card-title">{{$video->title}}</h5>
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
</div>
        </div>
    </div>
</div>
@endsection