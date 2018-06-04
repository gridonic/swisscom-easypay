<?php

namespace Gridonic\EasyPay\CheckoutPage;

/**
 * @package Gridonic\EasyPay\CheckoutPage
 */
class ProtocolDetectorService
{

    /**
     * Check for the http or https protocol by matching the given IP against defined ranges.
     *
     * The following ranges should return the http protocol:
     *
     * 178.197.248.0/22
     * 178.197.230.0/24
     * 178.197.238.0/24
     * 178.197.224.0 - 178.197.229.127
     * 178.197.232.0 - 178.197.237.127
     *
     * @param string $ip
     *
     * @return string 'http' or 'https'
     */
    public function detect($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return 'https';
        }

        $ranges = [
            '178.197.248.0/22',
            '178.197.230.0/24',
            '178.197.238.0/24',
            '178.197.224.0/22',
            '178.197.228.0/24',
            '178.197.229.0/25',
            '178.197.232.0/22',
            '178.197.236.0/24',
            '178.197.237.0/25',
        ];

        foreach ($ranges as $range) {
            if ($this->isIpInRange($ip, $range)) {
                return 'http';
            }
        }

        return 'https';
    }

    /**
     * @param string $ip
     * @param string $range
     * @return bool
     */
    protected function isIpInRange(string $ip, string $range)
    {
        if (strpos($range, '/') === false) {
            $range .= '/32';
        }

        // $range is in IP/CIDR format eg 127.0.0.1/24
        list($range, $netmask) = explode('/', $range, 2);
        $rangeDecimal = ip2long($range);
        $ipDecimal = ip2long($ip);
        $wildcardDecimal = pow(2, (32 - $netmask)) - 1;
        $netmaskDecimal = ~$wildcardDecimal;

        return (($ipDecimal & $netmaskDecimal) == ($rangeDecimal & $netmaskDecimal));
    }

}
