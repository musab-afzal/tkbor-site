{{-- Social icons driven by Website Settings. A channel with no URL is hidden. --}}
@php
    $socialChannels = [
        ['url' => $site->get('facebook_url'), 'icon' => 'fab fa-facebook', 'label' => 'Facebook'],
        ['url' => $site->get('twitter_url'), 'icon' => 'fab fa-twitter', 'label' => 'X'],
        ['url' => $site->get('linkedin_url'), 'icon' => 'fab fa-linkedin-in', 'label' => 'Linkedin'],
        ['url' => $site->get('instagram_url'), 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        ['url' => $site->get('youtube_url'), 'icon' => 'fab fa-youtube', 'label' => 'YouTube'],
    ];

    $socialChannels = array_filter($socialChannels, fn ($channel) => filled($channel['url']));
@endphp

@foreach ($socialChannels as $channel)
    <a href="{{ $channel['url'] }}" target="_blank" rel="noopener">
        <span class="social-links__icon">
            <i class="{{ $channel['icon'] }}" aria-hidden="true"></i>
            <span class="sr-only">{{ $channel['label'] }}</span>
        </span>
    </a>
@endforeach
