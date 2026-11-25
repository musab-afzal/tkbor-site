{{-- One team member card. Expects $member and $index. --}}
@php
    $memberPhoto = $member->image
        ? uploaded_url($member->image)
        : placeholder_image('assets/images/team/team-1-%d', $index ?? 0, 6);
@endphp

<div class="team-card">
    <div class="team-card__image grid-reveal-anim">
        <img loading="lazy" src="{{ $memberPhoto }}" alt="{{ $member->name }}" data-animate="true" class="grid-reveal-anim-img">
    </div>
    <div class="team-card__content" data-after-bg="{{ $memberPhoto }}">
        <div class="team-card__content__box">
            <p class="team-card__designation">{{ $member->designation }}</p>
            <h3 class="team-card__name"><a href="{{ route('team.show', $member) }}">{{ $member->name }}</a></h3>
            <ul class="team-card__social list-unstyled">
                @if ($member->facebook_url)<li><a href="{{ $member->facebook_url }}" target="_blank" rel="noopener">Fb.</a></li>@endif
                @if ($member->twitter_url ?? null)<li><a href="{{ $member->twitter_url }}" target="_blank" rel="noopener">X.</a></li>@endif
                @if ($member->instagram_url)<li><a href="{{ $member->instagram_url }}" target="_blank" rel="noopener">In.</a></li>@endif
                @if ($member->linkedin_url)<li><a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener">Ln.</a></li>@endif
                @if ($member->github_url)<li><a href="{{ $member->github_url }}" target="_blank" rel="noopener">Gh.</a></li>@endif
            </ul>
        </div>
    </div>
</div>
