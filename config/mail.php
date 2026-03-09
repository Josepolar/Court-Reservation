<?php
/**
 * Mail Configuration
 * Gmail API + OAuth2 (refresh token flow)
 */
return [
    'enabled' => filter_var($_ENV['MAIL_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? '',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? ($_ENV['APP_NAME'] ?? 'Court Reservation'),
    'google' => [
        'client_id' => $_ENV['GOOGLE_OAUTH_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['GOOGLE_OAUTH_CLIENT_SECRET'] ?? '',
        'refresh_token' => $_ENV['GOOGLE_OAUTH_REFRESH_TOKEN'] ?? '',
        'token_url' => $_ENV['GOOGLE_OAUTH_TOKEN_URL'] ?? 'https://oauth2.googleapis.com/token',
        'gmail_send_url' => $_ENV['GOOGLE_GMAIL_SEND_URL'] ?? 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send',
    ],
];
