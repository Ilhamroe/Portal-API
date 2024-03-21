<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment_content' => 'required',
        ]);

        $request['user_id'] = auth()->user()->id;

        $comment = Comment::create($request->all());

        return new CommentResource($comment->loadMissing(['commenter:id,username']));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comment_content' => 'required',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update($request->only('comment_content'));

        return new CommentResource($comment->loadMissing(['commenter:id,username']));
    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return new CommentResource($comment->loadMissing(['commenter:id,username']));
    }
}
