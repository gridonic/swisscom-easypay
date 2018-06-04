<?php

namespace Gridonic\EasyPay\Tests;

use Gridonic\EasyPay\CheckoutPage\ProtocolDetectorService;
use PHPUnit\Framework\TestCase;

/**
 * @package Gridonic\EasyPay\Tests
 */
class ProtocolDetectorServiceTest extends TestCase
{
    /**
     * @dataProvider clientIpDataProvider
     */
    public function testDetectHttpOrHttps_DifferentClientIps_ReturnsCorrectProtocol(string $ip, string $expectedProtocol)
    {
        $protocolDetectorService = new ProtocolDetectorService();

        $this->assertEquals($expectedProtocol, $protocolDetectorService->detect($ip));
    }

    /**
     * @return array
     */
    public function clientIpDataProvider()
    {
        return [
            ['', 'https'],
            ['invalid.ip', 'https'],
            ['178.197.248.10', 'http'],
            ['178.197.230.111', 'http'],
            ['178.197.238.50', 'http'],
            ['178.197.224.2', 'http'],
            ['178.197.228.127', 'http'],
            ['178.197.234.244', 'http'],
            ['178.197.237.127', 'http'],
            ['178.197.223.255', 'https'],
            ['178.197.237.128', 'https'],
            ['192.168.1.15', 'https'],
        ];
    }
}
