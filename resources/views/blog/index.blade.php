@extends('layouts.app')

@section('title', 'Blog - DailyForever')
@section('meta_description', 'Read the latest articles about privacy, security, encryption, and secure data sharing on the DailyForever blog.')
@section('keywords', 'privacy blog, security articles, encryption news, data protection, secure sharing, privacy tips, cybersecurity')
@section('og_type', 'blog')
@section('og_title', 'DailyForever Blog - Privacy & Security Articles')
@section('og_description', 'Read the latest articles about privacy, security, encryption, and secure data sharing on the DailyForever blog.')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="content-card hero-card p-6 sm:p-8 mb-6">
        <h1 class="text-3xl sm:text-4xl font-semibold text-yt-text text-center sm:text-left" data-i18n="blog.index.title">Blog</h1>
        <p class="mt-2 text-yt-text-secondary text-center sm:text-left" data-i18n="blog.index.tagline">Privacy & Security Insights. Zero‑knowledge, encryption, and safe sharing.</p>
        <div class="mt-4">
            <label for="blogSearch" class="sr-only" data-i18n="blog.index.search_label">Search articles</label>
            <input id="blogSearch" type="search" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder,aria-label" data-i18n-placeholder="blog.index.search_placeholder" data-i18n-aria-label="blog.index.search_label" placeholder="Search articles..." aria-label="Search articles">
        </div>
    </div>

    <!-- AdSense Banner Ad -->
    <x-adsense 
        slot="{{ env('ADSENSE_BANNER_SLOT') }}" 
        format="horizontal" 
        class="mb-6" 
    />

    <div id="blogList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="content-card block p-6 blog-card" data-title="{{ strtolower($post->title) }}" data-excerpt="{{ strtolower($post->excerpt ?? '') }}">
            <div class="text-xs text-yt-text-secondary">{{ optional($post->published_at)->format('M j, Y') }}</div>
            <h2 class="mt-1 text-xl font-medium text-yt-text">{{ $post->title }}</h2>
            @if($post->excerpt)
            <p class="text-sm mt-2 text-yt-text-secondary">{{ $post->excerpt }}</p>
            @endif
            <div class="mt-4"><span class="btn-secondary inline-block px-3 py-1.5 text-sm" data-i18n="blog.index.read_article">Read article</span></div>
        </a>
        @endforeach
    </div>
    <div id="noResults" class="hidden text-center text-yt-text-secondary text-sm mt-6" data-i18n="blog.index.no_results">No articles match your search.</div>

    <div class="mt-6">{{ $posts->links() }}</div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('blogSearch');
  if (!input) return;
  const cards = Array.from(document.querySelectorAll('.blog-card'));
  const noRes = document.getElementById('noResults');
  function norm(s){ return (s || '').toLowerCase().trim(); }
  function applyFilter() {
    const q = norm(input.value);
    let visible = 0;
    cards.forEach(card => {
      const t = card.getAttribute('data-title') || '';
      const e = card.getAttribute('data-excerpt') || '';
      const show = !q || t.includes(q) || e.includes(q);
      card.classList.toggle('hidden', !show);
      if (show) visible++;
    });
    if (noRes) noRes.classList.toggle('hidden', visible !== 0);
  }
  input.addEventListener('input', applyFilter);
});
</script>
@endsection
