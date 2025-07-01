@props(['text' => false, 'href' => '#'])
<li>
    <a href="{{ $href }}">{{ $text }}</a>
    {{ $slot }}
</li>