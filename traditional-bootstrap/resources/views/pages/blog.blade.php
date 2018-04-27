@extends('templates.public', [ 'title' => 'Blog' ])

@section('content')
    <div class="blog-page-component">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2 col-xxl-6 offset-xxl-3">
                    <h1>Blog</h1>

                    @foreach(App\Models\Blog::getBlogEntries() as $entry)
                        <div class="blog-entry">
                            @if($entry['headerimage'] != '')
                                <div
                                    class="blog-entry-header-image"
                                    style="background-image: url({{ $entry['headerimage'] }})">
                                </div>
                            @endif

                            <div class="blog-entry-content">
                                <h2 class="blog-entry-content-title">{{ $entry['title'] }}</h2>

                                <div class="blog-entry-content-info">
                                    <span class="blog-entry-content-info-name">{{ $entry['username'] }}</span> |
                                    <span class="blog-entry-content-info-date">{{ $entry['date'] }}</span>
                                </div>

                                <p class="blog-entry-content-body">
                                    {!! $entry['body'] !!}
                                </p>

                                <div class="blog-entry-content-taglist">
                                    @foreach($entry['tags'] as $tag)
                                        <span class="blog-entry-content-taglist-item">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
