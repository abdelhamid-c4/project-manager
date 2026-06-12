<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventSsrf
{
    private array $blockedHosts = [
        'localhost',
        '127.0.0.1',
        '0.0.0.0',
        '::1',
        '[::1]',
        '169.254.169.254', // AWS metadata
    ];

    private array $blockedSchemes = [
        'file',
        'ftp',
        'gopher',
        'dict',
    ];

    private array $privateIpRanges = [
        '10.',
        '172.16.',
        '172.17.',
        '172.18.',
        '172.19.',
        '172.20.',
        '172.21.',
        '172.22.',
        '172.23.',
        '172.24.',
        '172.25.',
        '172.26.',
        '172.27.',
        '172.28.',
        '172.29.',
        '172.30.',
        '172.31.',
        '192.168.',
    ];

    public function handle(Request $request, Closure $next)
    {
        $url = $request->input('url') ?? $request->query('url');

        if ($url && !$this->isUrlSafe($url)) {
            abort(403, 'Invalid or blocked URL');
        }

        return $next($request);
    }

    private function isUrlSafe(string $url): bool
    {
        $parsed = parse_url($url);

        if ($parsed === false) {
            return false;
        }

        // Check scheme
        $scheme = strtolower($parsed['scheme'] ?? 'http');
        if (in_array($scheme, $this->blockedSchemes, true)) {
            return false;
        }

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        // Check host
        $host = strtolower($parsed['host'] ?? '');
        if (in_array($host, $this->blockedHosts, true)) {
            return false;
        }

        // Check private IP ranges
        foreach ($this->privateIpRanges as $range) {
            if (str_starts_with($host, $range)) {
                return false;
            }
        }

        // Check for IP address in host
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            $ip = $host;
            if ($this->isPrivateIp($ip)) {
                return false;
            }
        }

        return true;
    }

    private function isPrivateIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $long = ip2long($ip);
            return $long !== false && (
                ($long >= ip2long('10.0.0.0') && $long <= ip2long('10.255.255.255')) ||
                ($long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255')) ||
                ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255')) ||
                ($long >= ip2long('127.0.0.0') && $long <= ip2long('127.255.255.255'))
            );
        }

        return false;
    }
}
