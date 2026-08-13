<x-mail::message>
# {{ $broadcast->title }}

{{ $broadcast->body }}

@if ($broadcast->url)
<x-mail::button :url="$broadcast->url">
    {{ $broadcast->label ?: __('broadcast::broadcast.open') }}
</x-mail::button>
@endif
</x-mail::message>
