<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ArticleRequest;
use App\Model\Article;
use App\Utility\Chinghwa\Helper\Temper;
use Carbon\Carbon;
use Request;

/**
 * Lastly, but most importantly, you may simply "type-hint" the dependency in the constructor of a class 
 * that is resolved by the container, including controllers, event listeners, queue jobs, middleware, and more
 */
class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::latest('published_at')->published()->get();

        return view('articles.index', ['title' => '文章', 'articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * stored data
     * 
     * @param  App\Http\Requests\ArticleRequest
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request)
    {
        Article::create($request->all());

        return redirect('articles');
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Model\Article $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  App\Model\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\ArticleRequest
     * @param  App\Model\Article $article
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article->update($request->all());

        return redirect('articles');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(){}
}
