<?php

use League\OAuth2\Client\Provider\GenericProvider;

/**
 * Sends email through Gmail API using OAuth2 refresh token.
 */
class OAuthEmailService
{
    public function isConfigured()
    {
        return (bool) config('mail.enabled', false)
            && config('mail.from_address')
            && config('mail.google.client_id')
            && config('mail.google.client_secret')
            && config('mail.google.refresh_token');
    }

    public function send($toEmail, $subject, $textBody, $toName = '', $htmlBody = null)
    {
        if (!$this->isConfigured()) {
            return $this->sendWithPhpMail($toEmail, $subject, $textBody, $toName, $htmlBody);
        }

        try {
            $provider = new GenericProvider([
                'clientId' => config('mail.google.client_id'),
                'clientSecret' => config('mail.google.client_secret'),
                'urlAuthorize' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'urlAccessToken' => config('mail.google.token_url'),
                'urlResourceOwnerDetails' => 'https://openidconnect.googleapis.com/v1/userinfo',
            ]);

            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => config('mail.google.refresh_token'),
            ]);

            $rawMessage = $this->buildRawMessage(
                config('mail.from_address'),
                config('mail.from_name', APP_NAME),
                $toEmail,
                $toName,
                $subject,
                $textBody,
                $htmlBody
            );

            return $this->sendViaGmailApi($accessToken->getToken(), $rawMessage);
        } catch (Throwable $e) {
            logError('OAuth email send failed', [
                'message' => $e->getMessage(),
                'to' => $toEmail,
                'subject' => $subject,
            ]);
            return $this->sendWithPhpMail($toEmail, $subject, $textBody, $toName, $htmlBody);
        }
    }

    private function buildRawMessage($fromEmail, $fromName, $toEmail, $toName, $subject, $textBody, $htmlBody)
    {
        $safeFromName = $this->sanitizeHeader($fromName);
        $safeToName = $this->sanitizeHeader($toName);
        $safeSubject = $this->sanitizeHeader($subject);

        $fromHeader = $safeFromName ? "{$safeFromName} <{$fromEmail}>" : $fromEmail;
        $toHeader = $safeToName ? "{$safeToName} <{$toEmail}>" : $toEmail;

        $headers = [
            "From: {$fromHeader}",
            "To: {$toHeader}",
            "Subject: {$safeSubject}",
            "MIME-Version: 1.0",
        ];

        if ($htmlBody !== null && $htmlBody !== '') {
            $boundary = 'b1_' . bin2hex(random_bytes(8));
            $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

            $body = "--{$boundary}\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $textBody . "\r\n\r\n";

            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $htmlBody . "\r\n\r\n";
            $body .= "--{$boundary}--";
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
            $headers[] = "Content-Transfer-Encoding: 8bit";
            $body = $textBody;
        }

        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    private function sendViaGmailApi($accessToken, $rawMessage)
    {
        $endpoint = config('mail.google.gmail_send_url');
        $payload = json_encode([
            'raw' => $this->base64UrlEncode($rawMessage),
        ]);

        if ($payload === false) {
            logError('Failed to encode Gmail API payload.');
            return false;
        }

        $response = null;
        $httpCode = 0;
        $curlError = '';

        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 20,
            ]);

            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Authorization: Bearer {$accessToken}\r\nContent-Type: application/json\r\n",
                    'content' => $payload,
                    'timeout' => 20,
                    'ignore_errors' => true,
                ],
            ]);
            $response = @file_get_contents($endpoint, false, $context);
            $statusLine = $http_response_header[0] ?? '';
            if (preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
                $httpCode = (int) $matches[1];
            }
        }

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            logError('Gmail API send failed', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $response,
            ]);
            return false;
        }

        return true;
    }

    private function sendWithPhpMail($toEmail, $subject, $textBody, $toName, $htmlBody)
    {
        $fromAddress = config('mail.from_address') ?: ('no-reply@' . parse_url(APP_URL, PHP_URL_HOST));
        $fromName = config('mail.from_name', APP_NAME);

        $safeSubject = $this->sanitizeHeader($subject);
        $safeToName = $this->sanitizeHeader($toName);
        $safeFromName = $this->sanitizeHeader($fromName);

        $toHeader = $safeToName ? "{$safeToName} <{$toEmail}>" : $toEmail;
        $fromHeader = $safeFromName ? "{$safeFromName} <{$fromAddress}>" : $fromAddress;

        $headers = [
            "From: {$fromHeader}",
            "Reply-To: {$fromAddress}",
            "MIME-Version: 1.0",
        ];

        $body = $textBody;
        if ($htmlBody !== null && $htmlBody !== '') {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $body = $htmlBody;
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }

        $ok = @mail($toHeader, $safeSubject, $body, implode("\r\n", $headers));
        if (!$ok) {
            logError('Fallback mail() send failed', [
                'to' => $toEmail,
                'subject' => $subject,
            ]);
        }

        return $ok;
    }

    private function sanitizeHeader($value)
    {
        return str_replace(["\r", "\n"], '', (string) $value);
    }

    private function base64UrlEncode($value)
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
