<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($posts as $post)
    <url>
        <loc>{{ url('/blog/'.$post->slug) }}</loc>
        @if($post->updated_at)
        <lastmod>{{ optional($post->updated_at)->toAtomString() }}</lastmod>
        @endif
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach
</urlset>
