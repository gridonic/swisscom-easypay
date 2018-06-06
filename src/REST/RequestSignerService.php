<?php

namespace Gridonic\EasyPay\REST;

use Gridonic\EasyPay\Environment\Environment;
use Gridonic\EasyPay\Signature\SignatureService;
use GuzzleHttp\Psr7\Request;

/**
 * Service class to sign a request for the EasyPay REST Api.
 *
 * @package Gridonic\EasyPay\REST
 */
class RequestSignerService
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var SignatureService
     */
    private $signatureService;

    /**
     * @param Environment $environment
     * @param SignatureService $signatureService
     */
    public function __construct(Environment $environment, SignatureService $signatureService)
    {
        $this->environment = $environment;
        $this->signatureService = $signatureService;
    }

    /**
     * Sign the given request for the EasyPay REST Api.
     *
     * Calculates the signature and adds required headers.
     *
     * @param Request $request
     *
     * @return Request
     */
    public function sign(Request $request)
    {
        $contentTypeHeader = $request->getHeader('Content-Type');
        $contentType = count($contentTypeHeader) ? array_pop($contentTypeHeader) : '';
        $body = (string) $request->getBody();
        $contentHash = ($body) ? base64_encode($this->signatureService->hash($body)) : '';
        $date = $this->date();

        $hash = $this->buildHash($request->getMethod(), $contentHash, $contentType, $date, $request->getUri());
        $signature = base64_encode($this->signatureService->sign($hash, $this->environment->getSecret()));

        return $request
            ->withAddedHeader('X-SCS-Signature', $signature)
            ->withAddedHeader('X-SCS-Date', $date)
            ->withAddedHeader('Content-MD5', $contentHash);
    }

    /**
     * @param string $requestMethod
     * @param string $contentHash
     * @param string $contentType
     * @param string $date
     * @param string $canonicalizedResource
     *
     * @return string
     */
    protected function buildHash(string $requestMethod, string $contentHash, string $contentType, string $date, string $canonicalizedResource)
    {
        return implode("\n", [
            $requestMethod,
            $contentHash,
            $contentType,
            $date,
            '/' . ltrim($canonicalizedResource, '/'),
        ]);
    }

    /**
     * @return string
     */
    protected function date()
    {
        return gmdate('D, d M Y H:i:s +0000', time());
    }
}
