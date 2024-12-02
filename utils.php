<?php

function safe_string(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function getRealIp(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }

    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        return $_SERVER['HTTP_X_REAL_IP'];
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function getRealReferer(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

    if (!empty($_SERVER['HTTP_REFERER'])) {
        $parsedUrl = parse_url($_SERVER['HTTP_REFERER']);
        if (!empty($parsedUrl['host'])) {
            return $protocol . $parsedUrl['host'];
        }
    }

    if (!empty($_SERVER['HTTP_HOST'])) {
        return $protocol . $_SERVER['HTTP_HOST'];
    }

    if (!empty($_SERVER['SERVER_NAME'])) {
        return $protocol . $_SERVER['SERVER_NAME'];
    }

    return 'unknown';
}

function sendCurlRequest(string $url, array $headers = [], $data = null, string $method = 'GET'): string
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'GET':
        default:
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL Error: $error_msg";
    }

    curl_close($ch);

    return $response;
}