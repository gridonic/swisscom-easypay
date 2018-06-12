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
 * Provides methods to query the Easypay REST API.
 *
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
     *
     * @return RESTApiService
     */
    public static function create(Environment $environment)
    {
        return new self(new Client(), new RequestSignerService($environment, new SignatureService()), $environment);
    }

    /**
     * Execute a direct payment for one-time-purchases over the checkout page.
     *
     * This method can be used to COMMIT, REJECT or REFUND a given payment.
     *
     * @param string $paymentId The payment ID obtained after the purchase on the checkout page.
     * @param string $operation COMMIT, REJECT or REFUND. Defaults to COMMIT.
     *
     * @throws RESTApiException Thrown if the API is not reachable due to network problems.
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
                    ->setAmount($body['amount'] ?? null)
                    ->setOperation($body['operation'] ?? null);
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Get all information about a direct payment.
     *
     * @param string $paymentId The payment ID obtained after the purchase on the checkout page.
     *
     * @throws RESTApiException Thrown if the API is not reachable due to network problems.
     *
     * @return DirectPaymentResponse
     */
    public function getDirectPayment(string $paymentId)
    {
        $uri = sprintf('payments/%s', $paymentId);
        $headers = array_merge($this->getDefaultHeaders(), ['Accept' => self::HEADER_DIRECT_PAYMENT]);

        $request = $this->requestSignerService->sign(new Request('GET', $uri, $headers));

        try {
            $httpResponse = $this->httpClient->send($request, $this->getDefaultRequestOptions());
            $response = new DirectPaymentResponse();

            if ($httpResponse->getStatusCode() === 200) {
                $body = json_decode((string) $httpResponse->getBody(), true);

                return $response
                    ->setIsSuccess(true)
                    ->setHttpStatusCode($httpResponse->getStatusCode())
                    ->setAmount($body['amount'] ?? null)
                    ->setOrderId($body['orderID'] ?? null)
                    ->setStatus($body['status'] ?? null)
                    ->setCreatedOn($body['createdOn'] ?? null)
                    ->setExtTransactionId($body['extTransactionId'] ?? null)
                    ->setIsAdultContent(isset($body['isAdultContent']) ? (bool) $body['isAdultContent'] : null)
                    ->setIsRoaming(isset($body['isRoaming']) ? (bool) $body['isRoaming'] : null)
                    ->setPaymentInfo($body['paymentInfo'] ?? null)
                    ->setUserSourceIp($body['userSourceIP'] ?? null)
                    ->setUserAgentOrigin($body['userAgentOrigin'] ?? null);
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Authorize a subscription payment.
     *
     * This method can be used to COMMIT, REJECT, REFUND, RENEW or CANCEL a given subscription payment.
     *
     * @param string $authSubscriptionId The subscription ID obtained after the purchase on the checkout page.
     * @param string $operation COMMIT, REJECT, REFUND, RENEW or CANCEL.
     * @param array $additionalData Some operations may need additional data to be submitted in the request body.
     *
     * @throws RESTApiException Thrown if the API is not reachable due to network problems.
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
                    ->setAmount($body['amount'] ?? null)
                    ->setOperation($body['operation'] ?? null)
                    ->setStartRefund($body['startRefund'] ?? null);
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Get all information about an authorized subscription.
     *
     * @param string $authSubscriptionId The subscription ID obtained after the purchase on the checkout page.
     *
     * @throws RESTApiException Thrown if the API is not reachable due to network problems.
     *
     * @return AuthSubscriptionResponse
     */
    public function getAuthorizeSubscription(string $authSubscriptionId)
    {
        $uri = sprintf('authsubscriptions/%s', $authSubscriptionId);
        $headers = array_merge($this->getDefaultHeaders(), ['Accept' => self::HEADER_AUTH_SUBSCRIPTION]);

        $request = $this->requestSignerService->sign(new Request('GET', $uri, $headers));

        try {
            $httpResponse = $this->httpClient->send($request, $this->getDefaultRequestOptions());
            $response = new AuthSubscriptionResponse();

            if ($httpResponse->getStatusCode() === 200) {
                $body = json_decode((string) $httpResponse->getBody(), true);

                return $response
                    ->setIsSuccess(true)
                    ->setHttpStatusCode($httpResponse->getStatusCode())
                    ->setAmount($body['amount'] ?? null)
                    ->setOrderId($body['orderID'] ?? null)
                    ->setStatus($body['status'] ?? null)
                    ->setExtTransactionId($body['extTransactionId'] ?? null)
                    ->setIsAdultContent(isset($body['isAdultContent']) ? (bool) $body['isAdultContent'] : null)
                    ->setIsRoaming(isset($body['isRoaming']) ? (bool) $body['isRoaming'] : null)
                    ->setIsActive(isset($body['isActive']) ? (bool) $body['isActive'] : null)
                    ->setDurationUnit($body['durationUnit'] ?? null)
                    ->setDuration($body['duration'] ?? null)
                    ->setMsidn($body['msisdn'] ?? null)
                    ->setCreatedOn($body['createdOn'] ?? null)
                    ->setUri($body['URI'] ?? null)
                    ->setNextPayment($body['nextPayment'] ?? null)
                    ->setStartRefund($body['startRefund'] ?? null)
                    ->setCpServiceId($body['cpServiceId'] ?? null)
                    ->setCpUserId($body['cpUserId'] ?? null)
                    ->setCpSubscriptionId($body['cpSubscriptionId'] ?? null);
            } else {
                return $this->buildErrorResponseFromHttpResponse($response, $httpResponse);
            }
        } catch (GuzzleException $e) {
            throw new RESTApiException($e->getMessage(), 0, $e);
        }
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
                ->setCode($message['code'] ?? null)
                ->setField($message['field'] ?? null)
                ->setMessage($message['message'] ?? null)
                ->setRequestId($message['requestId'] ?? null);
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
