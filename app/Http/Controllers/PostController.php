<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        // return response()->json(['data' => $post]);
        return PostDetailResource::collection($posts->loadMissing(['writer:id,username', 'commenter:id,post_id,user_id,comment_content']));
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);
        return new PostDetailResource($post->loadMissing(['writer:id,username', 'commenter:id,post_id,user_id,comment_content']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        $imageExtension = null;
        if ($request->file) {
            $fileName = $this->generateRandomString();
            $extension = $request->file->getClientOriginalExtension();
            $imageExtension = $fileName . '.' . $extension;

            Storage::putFileAs('image', $request->file, $imageExtension);
        }

        $request['image'] = $imageExtension;
        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        $post = Post::findOrFail($id);

        // Cek apakah ada file gambar yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($post->image) {
                Storage::delete('image/' . $post->image);
            }

            // Simpan gambar yang baru diunggah
            $image = $request->file('image');
            $fileName = $this->generateRandomString();
            $extension = $image->getClientOriginalExtension();
            $imageExtension = $fileName . '.' . $extension;
            Storage::putFileAs('image', $image, $imageExtension);

            // Perbarui kolom image dengan nama gambar yang baru
            $post->image = $imageExtension;
        }

        // Perbarui data post
        $post->update([
            'title' => $request->input('title'),
            'news_content' => $request->input('news_content'),
        ]);

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    function generateRandomString($length = 50)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

