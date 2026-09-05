<x-blogs-layout :theme="$theme" title="All Blogs">
    <h1 class="mb-5">All Blogs</h1>
    <div class="mb-3">
        <a href="{{ route('blogs.create', ['theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-primary" id="btn-create-new">Create New Blog</a>
    </div>

    <table class="table" id="blogs-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blogs as $blog)
                <tr>
                    <td>{{ $blog->title }}</td>
                    <td>
                        <a href="{{ route('blogs.show', ['blog' => $blog, 'theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-sm btn-info" id="btn-show-{{ $blog->id }}">Show</a>
                        <a href="{{ route('blogs.edit', ['blog' => $blog, 'theme' => $theme, 'use_xhr' => $useXhr ? 1 : 0]) }}" class="btn btn-sm btn-warning" id="btn-edit-{{ $blog->id }}">Edit</a>
                        <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-blogs-layout>
