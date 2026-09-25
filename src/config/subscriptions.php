<?php
// config/subscriptions.php
return [
    'plans' => [
        'basic' => [
            'name' => 'Basic',
            'price' => '10€',
            'max_teams' => 1,
            'max_players' => 25,
            'features' => ['club_profile'],
        ],
        'standard' => [
            'name' => 'Standard',
            'price' => '20€',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => ['club_profile', 'players', 'teams', 'matches', 'news'],
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => '50€',
            'max_teams' => 999,
            'max_players' => 9999,
            'features' => ['club_profile', 'players', 'teams', 'matches', 'news', 'advanced_stats', 'tactics'],
        ],
    ],
    'default_plan' => 'basic',
];