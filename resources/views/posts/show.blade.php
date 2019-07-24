@extends('layouts.master')

@section('content')

    <div class="blog-post">
        <h2 class="blog-post-title">{{ $post->title }}</h2>
        
        <p class="blog-post-meta">{{ $post->created_at->toFormattedDateString() }} autora <a href="#">{{ $post->user->name }}</a></p>

        @if (count($post->cats))
        <section class="mb-3">
            <h6 class="d-inline">Kategorija</h6>
            <a href="{{ route('cats.index',$post->cats['name']) }}" class="badge badge-success">{{ $post->cats['name'] }}</a>
        </section>
        @endif
        @if (count($post->tags))
        <section class="mb-3">
            <h6 class="d-inline">Tags:</h6>
            @foreach ($post->tags as $tag)
                <a href="{{ route('tags.index', $tag) }}" class="badge badge-primary">{{ $tag->name }}</a>
            @endforeach
        </section>
        @endif

        <section class="blog-body">
            {!! $post->body !!}
        </section>
    </div>

    <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
        {{ method_field('DELETE') }}
        {{ csrf_field() }}

        @if ($post->user_id === auth()->id())
        <div class="float-right">
            <a href="{{ route('posts.edit', $post->slug) }}" class="btn btn-info">Uredi</a>
            <button class="btn btn-danger">Obriši</button>
        </div>
        @endif
        
        <a class="btn btn-primary" href="{{ route('posts.index') }}">Natrag</a>
    </form>

    <br>

    <div class="card">
        <div class="card-body">
            <form action="/posts/{{ $post->slug }}/comments" method="post">
                @csrf

                <div class="form-group">
                    <textarea name="body" id="body" cols="30" class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}"></textarea>
                    @error('body')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Komentiraj</button>
                </div>

            </form>
        </div>
    </div>

    @if (count($post->comments))
    <hr>
    <div class="comments">
        <h4>Komentari:</h4>
        <ul class="list-group">
            @foreach ($post->comments as $comment)
            <li class="list-group-item">
                <b>{{ $comment->user->name }}</b>
                <i>{{ $comment->created_at->diffForHumans() }}</i>
                <p>{{ $comment->body }}</p>
            </li>
            @endforeach
        </ul>
    </div>
    @else
    <br>
    <p>Budi prvi koji će komentirati ovaj post!!!</p>
    @endif
    <br>

@endsection
