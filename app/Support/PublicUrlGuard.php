<?php

namespace App\Support;

class PublicUrlGuard
{
    /**
     * Confirm a URL uses HTTPS, matches an optional host allowlist, and resolves
     * only to publicly routable addresses.
     *
     * @param  string[]  $allowedHosts
     */
    public function allows(string $url, array $allowedHosts = []): bool
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ($parts['scheme'] ?? null) !== 'https' || empty($parts['host'])) {
            return false;
        }

        if (isset($parts['user']) || isset($parts['pass']) || (($parts['port'] ?? 443) !== 443)) {
            return false;
        }

        $host = strtolower(rtrim($parts['host'], '.'));
        $allowedHosts = array_map(fn (string $allowedHost): string => strtolower(rtrim($allowedHost, '.')), $allowedHosts);

        if ($allowedHosts !== [] && ! in_array($host, $allowedHosts, true)) {
            return false;
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : $this->resolveAddresses($host);

        return $addresses !== [] && collect($addresses)->every(
            fn (string $address): bool => filter_var(
                $address,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
            ) !== false,
        );
    }

    /**
     * @return string[]
     */
    protected function resolveAddresses(string $host): array
    {
        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        if (! is_array($records)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            fn (array $record): ?string => $record['ip'] ?? $record['ipv6'] ?? null,
            $records,
        ))));
    }
}
