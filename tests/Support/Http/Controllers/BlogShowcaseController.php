<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class BlogShowcaseController extends Controller
{
    public function index(Request $request): View
    {
        $blogId = $request->query('blog_id');
        $blog = $blogId ? Blog::find($blogId) : Blog::first();

        if (!$blog) {
            $blog = Blog::create([
                'title' => 'Test Blog Post',
                'content' => 'This is a test blog post content.',
            ]);
        }

        $theme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));

        config([
            'medialibrary-extensions.frontend_theme' => $theme,
            'medialibrary-extensions.use_xhr' => $useXhr,
        ]);

        return view('blog-showcase', [
            'blog' => $blog,
            'theme' => $theme,
            'useXhr' => $useXhr,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $id = $request->input('id');

        // Promotion logic for non-XHR (standard form submission)
        // The InteractsWithMediaExtended trait looks for client_token in the request.
        $clientTokenFromInput = (string) ($request->input('client_token') ?? '');
        $clientTokenFromCookie = (string) ($request->cookie('mle_client_token') ?? '');
        $effectiveClientToken = $clientTokenFromInput !== '' ? $clientTokenFromInput : $clientTokenFromCookie;

        if ($effectiveClientToken !== '' && $request->input('client_token') !== $effectiveClientToken) {
            $request->merge(['client_token' => $effectiveClientToken]);
        }

        if ($id) {
            $blog = Blog::findOrFail($id);
            $blog->update($request->only('title', 'content'));
        } else {
            $blog = Blog::create($request->only('title', 'content'));
        }

        return redirect()->route('blog-showcase', [
            'blog_id' => $blog->id,
            'theme' => $request->input('theme'),
            'use_xhr' => $request->input('use_xhr'),
        ]);
    }
}
