@php
    $mime = $media['mime_type'];
@endphp

@if (Str::contains($mime, 'video'))
    <video controls controlsList="nodownload" oncontextmenu="return false;" class="object-cover w-full h-full rounded-lg">
        <source src="{{ $media['original_url'] }}" type="{{ $mime }}">
        Your browser does not support the video tag.
    </video>
@elseif (Str::contains($mime, 'image'))
    <img src="{{ $media['original_url'] }}" class="object-cover w-full h-full rounded-lg" alt="Post Image">
@endif
