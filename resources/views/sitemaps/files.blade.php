<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($files as $file)
    <url>
        <loc>{{ url('/files/'.$file->identifier) }}</loc>
        @if($file->updated_at)
        <lastmod>{{ optional($file->updated_at)->toAtomString() }}</lastmod>
        @endif
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
@endforeach
</urlset>


