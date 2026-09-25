<p>Hello,</p>

<p>A new subscription requires approval.</p>

<ul>
    <li>User: {{ $subscription->user?->name }} ({{ $subscription->user?->email }})</li>
    <li>Club: {{ $subscription->club?->name }}</li>
    <li>Package: {{ $subscription->plan?->name ?? $subscription->plan_type }}</li>
    <li>Status: {{ $subscription->status }}</li>
</ul>

<p>Please open the Super Admin panel to approve or reject this subscription.</p>
