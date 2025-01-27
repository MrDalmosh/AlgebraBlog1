<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Mail\PostDeleted;
use App\Tag;
use App\Cat;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    public function index()
    {
        // $posts = DB::table('posts')->get();

        $posts = Post::latest()->get();

       // $popularPosts = Post::popular();

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        $viewed = session()->get('viewed_posts', []);

        if (!in_array($post->id, $viewed)) {
            session()->push('viewed_posts', $post->id);
            $post->increment('views');//$post->views = ++$viewed;
            $post->save();
        }

        return view('posts.show', compact('post'));
    }

    public function create(){

        $tags = Tag::all();
        $cats = Cat::all();
        return view('posts.create', compact('tags','cats'));
    }

    public function store(){


        request()->validate([
            'title' => 'required|min:3|max:255',
            'body'  => 'required|min:3|max:65535',
            'tags'  => 'required',
            'cats'  => 'required'
        ]);

        $post = Post::create([
            'title'  => request('title'),
            'body'    => request('body'),
            'user_id' => auth()->id()
        ]);

        $tags = request('tags');
        $cats = request('cats');
        $post->tags()->attach($tags);
        $post->cats()->attach($cats);

        return redirect()->route('posts.index')->withFlashMessage("Objava \"$post->title\" dodana uspješno");
    }

    public function edit(Post $post)
    {
        $tags = Tag::all();
        $cats = Cat::all();
        return view('posts.edit', compact('post', 'tags','cats'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'title' => 'required|min:3|max:255',
            'body'  => 'required|min:3|max:65535',
            'tags'  => 'required',
            'cats'  => 'required'
        ]);

        $post =Post::find($id);
        $post->title = $request['title'];
        $post->body = $request['body'];
        $post->slug = null;
        $post->save();

        $post->tags()->sync(request('tags'));
        $post->cats()->sync(request('cats'));

        return redirect()->route('posts.index')->withFlashMessage("Objava \"$post->title\" uspješno ažurirana.");
    }

    public function destroy($id)
    {                        
        $post = Post::find($id);
        $post->delete();

    //   \Mail::to($post->user)->send(new PostDeleted($post));

        return redirect()->route('posts.index')->withFlashMessage("Objava je uspješno obrisana.");
    }

}
