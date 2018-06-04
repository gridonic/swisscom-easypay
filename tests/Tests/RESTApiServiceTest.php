<?php

namespace Gridonic\EasyPay\Tests;

use Gridonic\EasyPay\REST\RequestSignerService;
use Gridonic\EasyPay\REST\RESTApiException;
use Gridonic\EasyPay\REST\RESTApiService;
use Gridonic\EasyPay\Environment\Environment;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @package Gridonic\EasyPay\Tests
 */
class RESTApiServiceTest extends TestCase
{
    public function testDirectPayment_InvalidOperation_ThrowsException()
    {
        $restApiService = $this->getRestApiService();

        $this->expectException(\InvalidArgumentException::class);
        $restApiService->directPayment('paymentId-123', 'COMIT');
    }

    public function testDirectPayment_ApiNotAvailable_ThrowsException()
    {
        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) {
            $dummyRequest = new Request('PUT', 'api');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->expects($this->once())
                ->method('send')
                ->will($this->throwException(new RequestException('', $dummyRequest)));
        });

        $this->expectException(RESTApiException::class);
        $restApiService->directPayment('paymentId-123');
    }

    public function testDirectPayment_ApiCallSuccessful_CorrectResponse()
    {
        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) {
            $dummyRequest = new Request('PUT', 'apiUri');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->method('send')
                ->willReturn(new Response(200, [], '{"operation":"COMMIT"}'));
        });

        $directPaymentResponse = $restApiService->directPayment('paymentId-123');

        $this->assertEquals(200, $directPaymentResponse->getHttpStatusCode());
        $this->assertTrue($directPaymentResponse->isSuccess());
        $this->assertEquals('COMMIT', $directPaymentResponse->getOperation());
        $this->assertEmpty($directPaymentResponse->getErrorMessages());
    }

    public function testDirectPayment_ApiCallNotSuccessful_CorrectResponse()
    {
        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) {
            $dummyRequest = new Request('PUT', 'apiUri');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->method('send')
                ->willReturn(new Response(400, [], '{"messages":[{"code":"code","message":"message","field":"field"}]}'));
        });

        $directPaymentResponse = $restApiService->directPayment('paymentId-123');

        $this->assertEquals(400, $directPaymentResponse->getHttpStatusCode());
        $this->assertFalse($directPaymentResponse->isSuccess());
        $this->assertNotEmpty($directPaymentResponse->getErrorMessages());
        $this->assertEquals('code', $directPaymentResponse->getErrorMessages()[0]->getCode());
        $this->assertEquals('message', $directPaymentResponse->getErrorMessages()[0]->getMessage());
        $this->assertEquals('field', $directPaymentResponse->getErrorMessages()[0]->getField());
    }

    /**
     * @param \closure|null $dependencyManipulator
     * @return RESTApiService
     */
    protected function getRestApiService(\closure $dependencyManipulator = null)
    {
        $httpClientMock = $this->createMock(ClientInterface::class);
        $requestSignerServiceMock = $this->createMock(RequestSignerService::class);
        $environmentMock = $this->createMock(Environment::class);

        if ($dependencyManipulator !== null) {
            $dependencyManipulator($httpClientMock, $requestSignerServiceMock, $environmentMock);
        }

        return new RESTApiService($httpClientMock, $requestSignerServiceMock, $environmentMock);
    }
}
