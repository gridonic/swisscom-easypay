<?php

namespace Gridonic\EasyPay\Signature;

/**
 * @package Gridonic\EasyPay\Signature
 */
class SignatureService
{
    /**
     * @param string $data
     * @param string $secret
     * @return string
     */
    public function sign(string $data, string $secret)
    {
        return hash_hmac('sha1', $data, $secret, true);
    }

    /**
     * @param string $data
     * @return string
     */
    public function hash(string $data)
    {
        return md5($data, true);
    }
}
