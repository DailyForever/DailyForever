@extends('layouts.app')

@section('title', $post->title . ' - DailyForever')
@section('meta_description', $post->excerpt ?: 'Read this article on the DailyForever blog about privacy, security, and encryption.')
@section('keywords')
privacy, security, encryption, data protection, {{ $post->title }}
@endsection
@section('og_type', 'article')
@section('og_title', $post->title . ' - DailyForever')
@section('og_description', $post->excerpt ?: 'Read this article on the DailyForever blog about privacy, security, and encryption.')
@section('canonical', route('blog.show', $post->slug))

@push('scripts')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt ?: 'Read this article on the DailyForever blog about privacy, security, and encryption.',
    'url' => route('blog.show', $post->slug),
    'datePublished' => optional($post->published_at)->toAtomString(),
    'dateModified' => optional($post->updated_at)->toAtomString(),
    'author' => [
        '@type' => 'Organization',
        'name' => 'DailyForever',
        'url' => url('/'),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'DailyForever',
        'url' => url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('android-chrome-512x512.png'),
        ],
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => route('blog.show', $post->slug),
    ],
    'inLanguage' => 'en-US',
    'keywords' => "privacy, security, encryption, data protection, {$post->title}",
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Blog',
            'item' => route('blog.index'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ],
    ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb Navigation (text-only) -->
    <nav class="mb-4 text-sm text-yt-text-secondary" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-1">
            <li><a href="{{ url('/') }}" class="hover:text-yt-text" data-i18n="blog.show.breadcrumb.home">Home</a></li>
            <li>/</li>
            <li><a href="{{ route('blog.index') }}" class="hover:text-yt-text" data-i18n="blog.show.breadcrumb.blog">Blog</a></li>
            <li class="hidden sm:inline">/</li>
            <li class="hidden sm:inline text-yt-text">{{ $post->title }}</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <div class="content-card hero-card p-6 sm:p-8 mb-6">
        <h1 class="text-2xl sm:text-3xl font-semibold text-yt-text">{{ $post->title }}</h1>
        <div class="mt-1 text-xs text-yt-text-secondary">{{ optional($post->published_at)->format('M j, Y') }}</div>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('blog.index') }}" class="btn-secondary px-3 py-1.5 text-sm" data-i18n="blog.show.back_to_blog">Back to Blog</a>
            <button type="button" class="btn-primary px-3 py-1.5 text-sm" onclick="copyPageUrl(this)" data-i18n="blog.show.copy_link">Copy Link</button>
        </div>
    </div>

    <article class="content-card p-6 space-y-3">
        <!-- AdSense In-Article Ad -->
        <x-adsense 
            slot="{{ env('ADSENSE_INARTICLE_SLOT') }}" 
            format="fluid" 
            class="my-6" 
        />

        <div class="prose prose-invert max-w-none">
            {!! $post->body !!}
        </div>

        <!-- AdSense Bottom Ad -->
        <x-adsense 
            slot="{{ env('ADSENSE_BOTTOM_SLOT') }}" 
            format="horizontal" 
            class="mt-6" 
        />
    </article>
</div>
@endsection

@section('scripts')
<script>
function copyPageUrl(btn){
  try {
    navigator.clipboard.writeText(window.location.href);
    const t = btn.textContent; 
    const copied = (window.I18N && typeof window.I18N.t === 'function') ? window.I18N.t('blog.show.copied') : 'Copied!';
    btn.textContent = copied;
    setTimeout(() => btn.textContent = t, 2000);
  } catch (_) {}
}
</script>
@endsection



