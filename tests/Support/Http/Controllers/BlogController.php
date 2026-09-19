<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Rules\MinMediaCount;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $blogs = Blog::orderByDesc('created_at')->get();
        $theme = $request->query('theme', 'bootstrap-5');
        $useXhr = $request->boolean('use_xhr', true);

        return view('blogs.index', compact('blogs', 'theme', 'useXhr'));
    }

    public function show(Blog $blog, Request $request): View
    {
        $theme = $request->query('theme', 'bootstrap-5');
        $useXhr = $request->boolean('use_xhr', true);

        return view('blogs.show', compact('blog', 'theme', 'useXhr'));
    }

    public function create(Request $request): View
    {
        $blog = new Blog();
        $mode = 'create';
        $theme = $request->query('theme', 'bootstrap-5');
        $useXhr = $request->boolean('use_xhr', true);

        return view('blogs.create', compact('blog', 'mode', 'theme', 'useXhr'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Handle client_token for promotion
        $clientTokenFromInput = (string) ($request->input('_mle_token') ?? ($request->input('mle_client_token') ?? ''));
        $clientTokenFromCookie = (string) ($request->cookie('mle_client_token') ?? '');
        $effectiveClientToken = $clientTokenFromInput !== '' ? $clientTokenFromInput : $clientTokenFromCookie;

        if ($effectiveClientToken !== '' && $request->input('_mle_token') !== $effectiveClientToken) {
            $request->merge(['_mle_token' => $effectiveClientToken]);
        }

        $request->validate([
            'title' => 'required',
            'featured_image' => ['required', new MinMediaCount(Blog::class, ['blog-main'], 1, multiple: false)],
            'gallery' => ['required', new MinMediaCount(Blog::class, ['blog-gallery'], 2, multiple: true)],
        ]);

        $blog = Blog::create($request->only('title', 'content'));

        return redirect()->route('blogs.show', [
            'blog' => $blog->id,
            'theme' => $request->input('theme', 'bootstrap-5'),
            'use_xhr' => $request->input('use_xhr', 1),
        ])->with('success', 'Blog created.');
    }

    public function edit(Blog $blog, Request $request): View
    {
        $mode = 'edit';
        $theme = $request->query('theme', 'bootstrap-5');
        $useXhr = $request->boolean('use_xhr', true);

        return view('blogs.edit', compact('blog', 'mode', 'theme', 'useXhr'));
    }

    public function update(Blog $blog, Request $request): RedirectResponse
    {
        // Handle client_token for promotion
        $clientTokenFromInput = (string) ($request->input('_mle_token') ?? ($request->input('mle_client_token') ?? ''));
        $clientTokenFromCookie = (string) ($request->cookie('mle_client_token') ?? '');
        $effectiveClientToken = $clientTokenFromInput !== '' ? $clientTokenFromInput : $clientTokenFromCookie;

        if ($effectiveClientToken !== '' && $request->input('_mle_token') !== $effectiveClientToken) {
            $request->merge(['_mle_token' => $effectiveClientToken]);
        }

        $request->validate([
            'title' => 'required',
            'featured_image' => ['required', new MinMediaCount($blog, ['blog-main'], 1, multiple: false)],
            'gallery' => ['required', new MinMediaCount($blog, ['blog-gallery'], 2, multiple: true)],
        ]);

        $blog->update($request->only('title', 'content'));

        return redirect()->route('blogs.show', [
            'blog' => $blog->id,
            'theme' => $request->input('theme', 'bootstrap-5'),
            'use_xhr' => $request->input('use_xhr', 1),
        ])->with('success', 'Blog updated.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $blog->delete();

        return redirect()->route('blogs.index')
            ->with('success', 'Blog deleted.');
    }
}
