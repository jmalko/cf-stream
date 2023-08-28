@extends('statamic::layout')
@section('title', 'Cloudflare Stream Videos')

@section('content')
    <header class="mb-6">
        <h1>Videos from Cloudflare Stream</h1>
    </header>

    <button type="button" id="uppy-select-files" class="btn btn-primary" >Upload videos</button>

      <link href="https://releases.transloadit.com/uppy/v3.14.1/uppy.min.css" rel="stylesheet" />    

      <div id="uppy"></div>    

      <script type="module">    
          import {    
              Uppy,    
              Tus,    
              Dashboard,
          } from 'https://releases.transloadit.com/uppy/v3.14.1/uppy.min.mjs';    
                                                    
          const uppy = new Uppy( 
          {
            restrictions: { 
                allowedFileTypes: ['video/*'] 
            }
          }
          );    
                                                    
          uppy    
              .use(Dashboard, { 
                  target: '#uppy', 
                  inline: false, 
                  trigger: '#uppy-select-files', 
                  height: 300
              })    
              .use(Tus, { 
                  endpoint: '/!/cf-stream/upload', 
                  chunkSize: 150 * 1024 * 1024
              })    
                                                    
      </script>

      <h2 class="mt-12 mb-4" >Uploaded videos</h2>
      <div class="grid" style="gap: 1rem; ">
          @foreach($videos as $video)
            <div class="card flex" style="gap: 1rem;">
                <div class="card-image">
                    <img style="height: 90px; width: 160px; border-radius: 5px; object-fit: cover;" src="{{ $video['thumbnail'] }}" alt="{{ $video['meta']['name'] }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $video['meta']['name'] }}</h5>
                    <p class="text-xs mb-2">Duration {{ ceil($video['duration'] / 60) }}m &bull; @if ($video['readyToStream']) Ready to Stream @else Encoding @endif </p>
                    <a href="{{ $video['preview'] }}" target="_blank" class="btn btn-xs btn-primary">Preview video</a>
                    <p class="text-xs mt-2"><code>{{ $video['uid'] }}</code></p>
                </div>
            </div>
          @endforeach
      </div>
@endsection
