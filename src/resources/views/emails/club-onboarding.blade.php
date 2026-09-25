<p>Hello {{ $user->name }},</p>

<p>Your club registration for <strong>{{ $club->name }}</strong> was received.</p>

<p>Use the link below within 48 hours to choose a subscription package and complete onboarding:</p>

<p><a href="{{ $onboardingUrl }}">{{ $onboardingUrl }}</a></p>

<p>If you did not create this registration, you can ignore this email.</p>
