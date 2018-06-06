<?php

namespace Gridonic\EasyPay\Signature;

/**
 * Service to sign and hash data for the signature calculation.
 *
 * @package Gridonic\EasyPay\Signature
 */
class SignatureService
{
    /**
     * Sign the given data with the given secret key.
     *
     * @param string $data
     * @param string $secret
     *
     * @return string
     */
    public function sign(string $data, string $secret)
    {
        return hash_hmac('sha1', $data, $secret, true);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function hash(string $data)
    {
        return md5($data, true);
    }
}
