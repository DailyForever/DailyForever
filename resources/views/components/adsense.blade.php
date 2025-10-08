@props([
    'slot' => null,
    'format' => 'auto',
    'responsive' => true,
    'style' => 'display:block',
    'class' => 'w-full'
])

@if(env('ADSENSE_PUBLISHER_ID') && $slot)
<div class="{{ $class }}">
    <ins class="adsbygoogle"
         style="{{ $style }}"
         data-ad-client="ca-pub-{{ env('ADSENSE_PUBLISHER_ID') }}"
         data-ad-slot="{{ $slot }}"
         @if($format !== 'auto')
         data-ad-format="{{ $format }}"
         @endif
         @if($responsive)
         data-full-width-responsive="true"
         @endif></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
@endif
