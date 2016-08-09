@extends('base')

@section('body')

<h1>Article
    <small>
        <a class="btn btn-sm btn-default btn-raised pull-right" href="{{action('ArticlesController@show')}}">
            <i class="glyphicon glyphicon-plus">新增</i>
        </a>
    </small>
</h1>
<hr>

@foreach ($articles as $article)
	<h3>
		<a href="{{ action('ArticlesController@show', ['id' => $article->id]) }}">{{ $article->title }}</a>        
	</h3>
	<p>{{ $article->body }}</p>
@endforeach

@stop