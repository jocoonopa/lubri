@extends('base')

@section('body')

<h1>Article</h1>
<hr>

@foreach ($articles as $article)
	<h3>
		<a href="{{ action('ArticlesController@show', ['id' => $article->id]) }}">{{ $article->title }}</a>
	</h3>
	<p>{{ $article->body }}</p>
@endforeach

@stop