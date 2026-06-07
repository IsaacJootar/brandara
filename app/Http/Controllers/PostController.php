<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create()
    {
        $brand = currentBrand();

        // Recent drafts for this brand — scoped by brand_id
        $drafts = Post::where('brand_id', $brand->id)
            ->where('status', 'draft')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('create.index', compact('brand', 'drafts'));
    }

    public function destroy(Request $request, string $brand, Post $post)
    {
        // Verify post belongs to this brand
        abort_if($post->brand_id !== currentBrand()->id, 403);

        $post->delete();

        return redirect()
            ->route('create', ['brand' => $brand])
            ->with('success', 'Draft deleted.');
    }
}
