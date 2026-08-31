<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Ili specifično: ['http://localhost:5177', 'http://localhost:5173']

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Dozvoljava sva zaglavlja, uključujući Authorization i Accept

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];