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
        // For tests, we use the first blog post or create one if it doesn't exist.
        // We don't use the complex DataSourceResolver here to keep it simple,
        // but we respect the theme and use_xhr parameters.
        $blog = Blog::first() ?? Blog::create([
            'title' => 'Test Blog Post',
            'content' => 'This is a test blog post content.',
        ]);

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
        $blog = Blog::firstOrFail();

        // Promotion logic for non-XHR (standard form submission)
        // The InteractsWithMediaExtended trait looks for client_token in the request.
        $clientTokenFromInput = (string) ($request->input('client_token') ?? '');
        $clientTokenFromCookie = (string) ($request->cookie('mle_client_token') ?? '');
        $effectiveClientToken = $clientTokenFromInput !== '' ? $clientTokenFromInput : $clientTokenFromCookie;

        if ($effectiveClientToken !== '' && $request->input('client_token') !== $effectiveClientToken) {
            $request->merge(['client_token' => $effectiveClientToken]);
        }

        $blog->update($request->only('title', 'content'));

        return redirect()->back();
    }
}
