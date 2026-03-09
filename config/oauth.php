<?php
/**
 * OAuth Configuration
 */
return [
    'google' => [
        'enabled' => filter_var($_ENV['GOOGLE_OAUTH_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'client_id' => $_ENV['GOOGLE_OAUTH_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['GOOGLE_OAUTH_CLIENT_SECRET'] ?? '',
        'redirect_uri' => $_ENV['GOOGLE_OAUTH_REDIRECT_URI'] ?? '',
        'auth_url' => $_ENV['GOOGLE_OAUTH_AUTH_URL'] ?? 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => $_ENV['GOOGLE_OAUTH_TOKEN_URL'] ?? 'https://oauth2.googleapis.com/token',
        'user_info_url' => $_ENV['GOOGLE_OAUTH_USERINFO_URL'] ?? 'https://openidconnect.googleapis.com/v1/userinfo',
    ],
];
