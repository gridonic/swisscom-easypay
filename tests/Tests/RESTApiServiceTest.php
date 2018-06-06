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
    public function testApiCalls_ApiNotAvailable_ThrowsException()
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
        $restApiService->getDirectPayment('paymentId-123');
        $restApiService->authorizeSubscription('authorizeId-123');
        $restApiService->getAuthorizeSubscription('authorizeId-123');
    }

    public function testApiCalls_ApiCallNotSuccessful_CorrectResponse()
    {
        // Represents messages in the expected format returned by the REST API in the error case.
        $dummyMessages = [
            'messages' => [
                ['code' => 'code', 'message' => 'message', 'field' => 'field'],
                ['code' => 'code2', 'message' => 'message2', 'field' => 'field2'],
            ]
        ];

        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) use ($dummyMessages) {
            $dummyRequest = new Request('PUT', 'apiUri');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->method('send')
                ->willReturn(new Response(400, [], json_encode($dummyMessages)));
        });

        $directPaymentResponse = $restApiService->directPayment('paymentId-123');
        $errorMessages = $directPaymentResponse->getErrorMessages();
        $errorMessage = $errorMessages[0];

        $this->assertEquals(400, $directPaymentResponse->getHttpStatusCode());
        $this->assertFalse($directPaymentResponse->isSuccess());
        $this->assertNotEmpty($directPaymentResponse->getErrorMessages());
        $this->assertEquals('code', $errorMessage->getCode());
        $this->assertEquals('message', $errorMessage->getMessage());
        $this->assertEquals('field', $errorMessage->getField());

        $authSubscriptionResponse = $restApiService->authorizeSubscription('paymentId-123');
        $errorMessages = $authSubscriptionResponse->getErrorMessages();
        $errorMessage = $errorMessages[1];

        $this->assertEquals(400, $authSubscriptionResponse->getHttpStatusCode());
        $this->assertFalse($authSubscriptionResponse->isSuccess());
        $this->assertNotEmpty($authSubscriptionResponse->getErrorMessages());
        $this->assertEquals('code2', $errorMessage->getCode());
        $this->assertEquals('message2', $errorMessage->getMessage());
        $this->assertEquals('field2', $errorMessage->getField());
    }

    public function testDirectPayment_InvalidOperation_ThrowsException()
    {
        $restApiService = $this->getRestApiService();

        $this->expectException(\InvalidArgumentException::class);
        $restApiService->directPayment('paymentId-123', 'COMIT');
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

    public function testGetDirectPayment_ApiCallSuccessful_CorrectResponse()
    {
        $responseBody = [
            'orderID' => 1234,
            'amount' => 'CHF 20',
            'status' => 'COMPLETED',
            'paymentInfo' => 'Payment Information',
        ];

        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) use ($responseBody) {
            $dummyRequest = new Request('PUT', 'apiUri');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->method('send')
                ->willReturn(new Response(200, [], json_encode($responseBody)));
        });

        $directPaymentResponse = $restApiService->getDirectPayment('paymentId-123');

        $this->assertEquals(200, $directPaymentResponse->getHttpStatusCode());
        $this->assertTrue($directPaymentResponse->isSuccess());
        $this->assertEmpty($directPaymentResponse->getErrorMessages());
        $this->assertEquals($responseBody['orderID'], $directPaymentResponse->getOrderId());
        $this->assertEquals($responseBody['amount'], $directPaymentResponse->getAmount());
        $this->assertEquals($responseBody['status'], $directPaymentResponse->getStatus());
        $this->assertEquals($responseBody['paymentInfo'], $directPaymentResponse->getPaymentInfo());
    }

    public function testAuthorizeSubscription_InvalidOperation_ThrowsException()
    {
        $restApiService = $this->getRestApiService();

        $this->expectException(\InvalidArgumentException::class);
        $restApiService->authorizeSubscription('authId-123', 'REFFUND');
    }

    public function testAuthorizeSubscription_ApiCallSuccessful_CorrectResponse()
    {
        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) {
            $dummyRequest = new Request('PUT', 'apiUri');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->method('send')
                ->willReturn(new Response(200, [], '{"operation":"COMMIT","startRefund":"startRefund"}'));
        });

        $autSubscriptionResponse = $restApiService->authorizeSubscription('authId-123');

        $this->assertEquals(200, $autSubscriptionResponse->getHttpStatusCode());
        $this->assertTrue($autSubscriptionResponse->isSuccess());
        $this->assertEquals('COMMIT', $autSubscriptionResponse->getOperation());
        $this->assertEquals('startRefund', $autSubscriptionResponse->getStartRefund());
        $this->assertEmpty($autSubscriptionResponse->getErrorMessages());
    }

    public function testGetAuthorizeSubscription_ApiCallSuccessful_CorrectResponse()
    {
        $responseBody = [
            'orderID' => 1234,
            'amount' => 'CHF 20',
            'extTransactionId' => 'trans-1234',
            'durationUnit' => 'WEEK',
            'duration' => 3,
            'nextPayment' => '2018-07-21',
        ];

        $restApiService = $this->getRestApiService(function($httpClientMock, $requestSignerServiceMock, $environmentMock) use ($responseBody) {
            $dummyRequest = new Request('PUT', 'apiUri');
            $requestSignerServiceMock
                ->method('sign')
                ->willReturn($dummyRequest);

            $httpClientMock
                ->method('send')
                ->willReturn(new Response(200, [], json_encode($responseBody)));
        });

        $directPaymentResponse = $restApiService->getAuthorizeSubscription('authId-123');

        $this->assertEquals(200, $directPaymentResponse->getHttpStatusCode());
        $this->assertTrue($directPaymentResponse->isSuccess());
        $this->assertEmpty($directPaymentResponse->getErrorMessages());
        $this->assertEquals($responseBody['orderID'], $directPaymentResponse->getOrderId());
        $this->assertEquals($responseBody['amount'], $directPaymentResponse->getAmount());
        $this->assertEquals($responseBody['extTransactionId'], $directPaymentResponse->getExtTransactionId());
        $this->assertEquals($responseBody['durationUnit'], $directPaymentResponse->getDurationUnit());
        $this->assertEquals($responseBody['duration'], $directPaymentResponse->getDuration());
        $this->assertEquals($responseBody['nextPayment'], $directPaymentResponse->getNextPayment());
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
