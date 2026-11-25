{{-- Renders one level of the CMS-managed navigation. Expects $items. --}}
@php
    /** Mark an item current when its path (or a child's) matches the request. */
    $navPath = static function ($url) {
        $path = trim((string) parse_url((string) $url, PHP_URL_PATH), '/');

        return $path === '' ? '/' : $path;
    };

    $navIsCurrent = static function ($item) use (&$navIsCurrent, $navPath) {
        if (request()->is($navPath($item->url))) {
            return true;
        }

        foreach ($item->children as $child) {
            if ($navIsCurrent($child)) {
                return true;
            }
        }

        return false;
    };
@endphp

@foreach ($items as $item)
    @continue(! $item->status)

    @php
        $children = $item->children->where('status', true);
        $current = $navIsCurrent($item);
    @endphp

    <li class="{{ $children->isNotEmpty() ? 'dropdown' : '' }} {{ $current ? 'current' : '' }}">
        <a href="{{ $item->url ?: '#' }}" @if ($item->target === '_blank') target="_blank" rel="noopener" @endif>
            @if ($item->icon)<i class="{{ $item->icon }}"></i> @endif{{ $item->label }}
        </a>

        @if ($children->isNotEmpty())
            <ul>
                @include('frontend.partials.nav-items', ['items' => $children])
            </ul>
        @endif
    </li>
@endforeach
