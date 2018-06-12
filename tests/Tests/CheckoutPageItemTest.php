<?php

namespace Gridonic\EasyPay\Tests;

use Gridonic\EasyPay\CheckoutPage\CheckoutPageItem;
use PHPUnit\Framework\TestCase;

/**
 * @package Gridonic\EasyPay\Tests
 */
class CheckoutPageItemTest extends TestCase
{
    public function testConstructor_ItemInitializedWithDataSet_PropertiesAreSetCorrectly()
    {
        $data = [
            'title' => 'title',
            'paymentInfo' => 'paymentInfo',
            'amount' => '19.90',
            'description' => 'description',
            'promotionAmount' => '10',
            'isAdultContent' => true,
            'isRoaming' => false,
            'msisdn' => '0800 123456789',
            'successUrl' => 'successUrl',
            'cancelUrl' => 'cancelUrl',
        ];

        $checkoutPageItem = new CheckoutPageItem($data);

        $this->assertEquals('title', $checkoutPageItem->getTitle());
        $this->assertEquals('paymentInfo', $checkoutPageItem->getPaymentInfo());
        $this->assertEquals('19.90', $checkoutPageItem->getAmount());
        $this->assertEquals('description', $checkoutPageItem->getDescription());
        $this->assertEquals('10', $checkoutPageItem->getPromotionAmount());
        $this->assertTrue($checkoutPageItem->isAdultContent());
        $this->assertFalse($checkoutPageItem->isRoaming());
        $this->assertEquals('0800 123456789', $checkoutPageItem->getMsisdn());
        $this->assertEquals('successUrl', $checkoutPageItem->getSuccessUrl());
        $this->assertEquals('cancelUrl', $checkoutPageItem->getCancelUrl());
    }

    public function testToArray_ItemInitializedWithDataSet_ReturnsOnlyNonNullValuesAndIgnoresNonExistingProperties()
    {
        $data = [
            'title' => 'title',
            'paymentInfo' => 'paymentInfo',
            'inexistingProperty' => 'I should not appear when calling ->toArray()',
        ];

        $checkoutPageItem = new CheckoutPageItem($data);

        $expectedData = [
            'title' => 'title',
            'paymentInfo' => 'paymentInfo',
        ];

        $this->assertEquals($expectedData, $checkoutPageItem->toArray());
    }

    public function testSetDurationUnit_InvalidDurationUnit_ShouldThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $checkoutPageItem = new CheckoutPageItem();
        $checkoutPageItem
            ->setDurationUnit('YEAR');

        $checkoutPageItem2 = new CheckoutPageItem(['durationUnit' => 'YEAR']);
    }
}
