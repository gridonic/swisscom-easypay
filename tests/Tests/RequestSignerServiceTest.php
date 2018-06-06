<?php

namespace Gridonic\EasyPay\Tests;

use Gridonic\EasyPay\Environment\Environment;
use Gridonic\EasyPay\REST\RequestSignerService;
use Gridonic\EasyPay\Signature\SignatureService;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

/**
 * @package Gridonic\EasyPay\Tests
 */
class RequestSignerServiceTest extends TestCase
{
    public function testSign_PUTRequest_AddsRequiredHeaders()
    {
        $dummyRequest = new Request('PUT', 'https://api.url.com', [], 'body');

        $requestSignerService = $this->getRequestSignerService(function ($environmentMock, $signatureServiceMock) use ($dummyRequest) {
           $environmentMock
               ->expects($this->once())
               ->method('getSecret')
               ->willReturn('s3cr3t');

            $signatureServiceMock
                ->expects($this->once())
               ->method('sign')
               ->willReturn('signatureHash');

            $signatureServiceMock
                ->expects($this->once())
                ->method('hash')
                ->with((string) $dummyRequest->getBody())
                ->willReturn('contentHash');
        });

        $expectedSignatureHeader = base64_encode('signatureHash');
        $expectedContentHashHaeder = base64_encode('contentHash');

        $signedRequest = $requestSignerService->sign($dummyRequest);

        $this->assertEquals($expectedSignatureHeader, $signedRequest->getHeader('X-SCS-Signature')[0]);
        $this->assertEquals($expectedContentHashHaeder, $signedRequest->getHeader('Content-MD5')[0]);
        $this->assertTrue($signedRequest->hasHeader('X-SCS-Date'));
    }

    public function testSign_GETRequest_AddsRequiredHeadersAndDoesNotCalculateTheContentHash()
    {
        $dummyRequest = new Request('GET', 'https://api.url.com');

        $requestSignerService = $this->getRequestSignerService(function ($environmentMock, $signatureServiceMock) use ($dummyRequest) {
            $environmentMock
                ->expects($this->once())
                ->method('getSecret')
                ->willReturn('s3cr3t');

            $signatureServiceMock
                ->expects($this->once())
                ->method('sign')
                ->willReturn('signatureHash');

            // Get request do not calculate the content hash
            $signatureServiceMock
                ->expects($this->never())
                ->method('hash');
        });

        $expectedSignatureHeader = base64_encode('signatureHash');

        $signedRequest = $requestSignerService->sign($dummyRequest);

        $this->assertEquals($expectedSignatureHeader, $signedRequest->getHeader('X-SCS-Signature')[0]);
        $this->assertEquals('', $signedRequest->getHeader('Content-MD5')[0]);
        $this->assertTrue($signedRequest->hasHeader('X-SCS-Date'));
    }

    /**
     * @param \closure|null $dependencyManipulator
     * @return RequestSignerService
     */
    protected function getRequestSignerService(\closure $dependencyManipulator = null)
    {
        $environmentMock = $this->createMock(Environment::class);
        $signatureServiceMock = $this->createMock(SignatureService::class);

        if ($dependencyManipulator !== null) {
            $dependencyManipulator($environmentMock, $signatureServiceMock);
        }

        return new RequestSignerService($environmentMock, $signatureServiceMock);
    }
}
