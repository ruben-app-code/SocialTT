<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): Response
    {
        $comments = Comment::all();

        return view('creator.comment.index', [
            'comments' => $comments,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('creator.comment.create');
    }

    public function store(CommentStoreRequest $request): Response
    {
        $comment = Comment::create($request->validated());

        $request->session()->flash('comment.id', $comment->id);

        return redirect()->route('comments.index');
    }

    public function show(Request $request, Comment $comment): Response
    {
        return view('creator.comment.show', [
            'comment' => $comment,
        ]);
    }

    public function edit(Request $request, Comment $comment): Response
    {
        return view('creator.comment.edit', [
            'comment' => $comment,
        ]);
    }

    public function update(CommentUpdateRequest $request, Comment $comment): Response
    {
        $comment->update($request->validated());

        $request->session()->flash('comment.id', $comment->id);

        return redirect()->route('comments.index');
    }

    public function destroy(Request $request, Comment $comment): Response
    {
        $comment->delete();

        return redirect()->route('comments.index');
    }
}