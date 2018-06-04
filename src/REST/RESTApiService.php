<?php

namespace Gridonic\EasyPay\REST;

use Gridonic\EasyPay\Environment\Environment;
use Gridonic\EasyPay\Signature\SignatureService;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * @package Gridonic\EasyPay\Request
 */
class RESTApiService
{
    const HEADER_DIRECT_PAYMENT = 'application/vnd.ch.swisscom.easypay.direct.payment+json';
    const HEADER_AUTH_SUBSCRIPTION = 'application/vnd.ch.swisscom.easypay.authsubscription+json';
    const HEADER_MESSAGE_LIST = 'application/vnd.ch.swisscom.easypay.message.list+json';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var RequestSignerService
     */
    private $requestSignerService;

    /**
     * @param ClientInterface $httpClient
     * @param RequestSignerService $requestSignerService
     * @param Environment $environment
     */
    public function __construct(ClientInterface $httpClient, RequestSignerService $requestSignerService, Environment $environment)
    {
        $this->httpClient = $httpClient;
        $this->requestSignerService = $requestSignerService;
        $this->environment = $environment;
    }

    /**
     * @param Environment $environment
     * @return RESTApiService
     */
    public static function create(Environment $environment)
    {
        return new self(new Client(), new RequestSignerService($environment, new SignatureService()), $environment);
    }

    /**
     * @param string $paymentId
     * @param string $operation
     *
     * @throws RESTApiException
     *
     * @return DirectPaymentResponse
     */
    public function directPayment(string $paymentId, $operation = 'COMMIT')
    {
        if (!in_array($operation, ['COMMIT', 'REJECT', 'REFUND'])) {
            throw new \InvalidArgumentException("Mode must be one of ['COMMIT', 'REJECT', 'REFUND']");
        }

        $uri = sprintf('payments/%s', $paymentId);
        $body = json_encode(['operation' => $operation]);
        $headers = array_merge(
            $this->getDefaultHeaders(),
            [
                'Content-Type' => self::HEADER_DIRECT_PAYMENT,
                'Accept' => self::HEADER_MESSAGE_LIST,
            ]
        );

        $request = $this->requestSignerService->sign(new Request('PUT', $uri, $headers, $body));

        try {
            $httpResponse = $this->httpClient->send($request, $this->getDefaultRequestOptions());
            $response = new DirectPaymentResponse();

            if ($httpResponse->getStatusCode() === 200) {
                $body = json_decode((string) $httpResponse->getBody(), true);

                return $response
                    ->setIsSuccess(true)
                    ->setHttpStatusCode(200)
                    ->setAmount($body['amount'] ?? '')
                    ->setOperation($body['operation'] ?? '');
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $paymentId
     * @return AuthSubscriptionResponse|DirectPaymentResponse
     * @throws RESTApiException
     */
    public function getDirectPayment(string $paymentId)
    {
        $uri = sprintf('payments/%s', $paymentId);
        $headers = array_merge(
            $this->getDefaultHeaders(),
            [
                'Content-Type' => self::HEADER_DIRECT_PAYMENT,
                'Accept' => self::HEADER_MESSAGE_LIST,
            ]
        );

        $request = $this->requestSignerService->sign(new Request('GET', $uri, $headers));

        try {
            $httpResponse = $this->httpClient->send($request, $this->getDefaultRequestOptions());
            $response = new DirectPaymentResponse();

            if ($httpResponse->getStatusCode() === 200) {
                $body = json_decode((string) $httpResponse->getBody(), true);

                return $response
                    ->setIsSuccess(true)
                    ->setHttpStatusCode($httpResponse->getStatusCode())
                    ->setAmount($body['amount'] ?? '')
                    ->setOrderId($body['orderId'] ?? '')
                    ->setIsAdultContent(isset($body['isAdultContent']) && $body['isAdultContent'] ? true : false)
                    ->setIsRoaming(isset($body['isRoaming']) && $body['isRoaming'] ? true : false)
                    ->setPaymentInfo($body['paymentInfo'] ?? '')
                    ->setUserSourceIp($body['userSourceIP'] ?? '')
                    ->setUserAgentOrigin($body['userAgentOrigin'] ?? '');
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $authSubscriptionId
     * @param string $operation
     * @param array $additionalData
     *
     * @throws RESTApiException
     *
     * @return AuthSubscriptionResponse
     */
    public function authorizeSubscription(string $authSubscriptionId, $operation = 'COMMIT', array $additionalData = [])
    {
        if (!in_array($operation, ['COMMIT', 'REJECT', 'REFUND', 'RENEW', 'CANCEL'])) {
            throw new \InvalidArgumentException("Mode must be one of ['COMMIT', 'REJECT', 'REFUND', 'RENEW', 'CANCEL']");
        }

        $uri = sprintf('authsubscriptions/%s', $authSubscriptionId);
        $body = json_encode(array_merge(['operation' => $operation], $additionalData));
        $headers = array_merge(
            $this->getDefaultHeaders(),
            [
                'Content-Type' => self::HEADER_AUTH_SUBSCRIPTION,
                'Accept' => self::HEADER_MESSAGE_LIST,
            ]
        );

        $request = $this->requestSignerService->sign(new Request('PUT', $uri, $headers, $body));

        try {
            $httpResponse = $this->httpClient->send($request, $this->getDefaultRequestOptions());
            $response = new AuthSubscriptionResponse();

            if ($httpResponse->getStatusCode() === 200) {
                $body = json_decode((string) $httpResponse->getBody(), true);

                return $response
                    ->setIsSuccess(true)
                    ->setHttpStatusCode($httpResponse->getStatusCode())
                    ->setAmount($body['amount'] ?? '')
                    ->setOperation($body['operation'] ?? '')
                    ->setStartRefund($body['startRefund'] ?? '');
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
    }

    public function getAuthorizeSubscription(string $authSubscriptionId)
    {
    }

    /**
     * @param RESTApiResponseInterface $response
     * @param ResponseInterface $httpResponse
     *
     * @return DirectPaymentResponse|AuthSubscriptionResponse
     */
    protected function buildErrorResponseFromHttpResponse(RESTApiResponseInterface $response, ResponseInterface $httpResponse)
    {
        $body = json_decode((string) $httpResponse->getBody(), true);
        $messages = $body['messages'] ?? [];

        $errorMessages = array_map(function ($message) {
            $errorMessage = new ErrorMessage();
            return $errorMessage
                ->setCode($message['code'] ?? '')
                ->setField($message['field'] ?? '')
                ->setMessage($message['message'] ?? '')
                ->setRequestId($message['requestId'] ?? '');
        }, $messages);

        return $response
            ->setIsSuccess(false)
            ->setHttpStatusCode($httpResponse->getStatusCode())
            ->setErrorMessages($errorMessages);
    }

    /**
     * @return array
     */
    protected function getDefaultRequestOptions()
    {
        $baseUri = sprintf('https://%s/ce-rest-service/', $this->environment->getHost());

        return ['base_uri' => $baseUri, 'http_errors' => false];
    }

    /**
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return [
            'X-CE-Client-Specification-Version' => '1.1',
            'X-Request-Id' => $this->generateRandomRequestId(),
            'X-Merchant-Id' => $this->environment->getMerchantId(),
        ];
    }

    /**
     * @return string
     */
    protected function generateRandomRequestId()
    {
        return uniqid('gridonic-swisscom-easypay');
    }
}
