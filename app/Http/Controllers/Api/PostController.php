<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Post;

class PostController extends Controller
{
    public function getIndex()
    {
        return Post::with('user')->paginate();
    }

    public function getDetail($id)
    {
        $post = Post::with('user')->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(['data' => $post]);
    }

    public function postCreate(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'intro' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'required|in:0,1'
        ]);

        $post = Post::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'intro' => $data['intro'],
            'description' => $data['description'],
            'is_active' => $data['is_active']
        ]);

        return response()->json(['data' => $post], 201);
    }

    public function patchEdit(Request $request, $id)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'intro' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'required|in:0,1'
        ]);

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post = tap($post)->update([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'intro' => $data['intro'],
            'description' => $data['description'],
            'is_active' => $data['is_active']
        ]);

        return response()->json(['data' => $post]);
    }

    public function deletePost($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Success delete post']);
    }
}
