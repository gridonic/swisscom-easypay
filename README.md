# Swisscom Easypay

A PHP library to manage payments with Swisscom Easypay.

## Installation

Install the library with composer: 

```
composer require gridonic/swisscom-easypay
```

## Basic Usage

Note: This guide only covers the basics on how to use this library. More details about Easypay can be found
in the [official documentations](https://www.swisscom.ch/en/business/enterprise/offer/value-added-services/business-numbers/technische-dokumentation-business-numbers.html).

### Environment

Create a new `STAGING` or `PROD` environment based on your credentials:

```php
use Gridonic\EasyPay\Environment\Environment;

$prodEnvironment = new Environment(Environment::ENV_PROD, 'my-merchant-id', 'my-secret-key')
$stagingEnvironment = new Environment(Environment::ENV_STAGING, 'my-merchant-id', 'my-secret-key')
```

### Checkout page

Redirect the user to the Easypay checkout page where the purchase must be confirmed.

1. Map the user's shopping cart to a `CheckoutPageItem`. Note that you must provide the 
_success/error/cancel_ urls for the redirect back to your shop.
2. In case of a recurrent service, make sure to pass the duration and duration unit to
 the checkout page item via `setDuration()` and `setDurationUnit()`. 
3. Call `CheckoutPageService::getCheckoutPageUrl()` to obtain the redirect url.

```php
use Gridonic\EasyPay\CheckoutPage\CheckoutPageItem;
use Gridonic\EasyPay\CheckoutPage\CheckoutPageService;

// Map the user's shopping cart to a CheckoutPageItem
$checkoutPageItem = new CheckoutPageItem();
$checkoutPageItem
    ->setTitle('A title displayed on the checkout page')
    ->setDescription('A description displayed on the checkout page')
    ->setPaymentInfo('Some payment information, visible on the invoice of the customer')
    ->setAmount('99.90')
    ->setSuccessUrl('https://myshop.com/return')
    ->setErrorUrl('https://myshop.com/return')
    ->setCancelUrl('https://myshop.com/cancel')

// Get the checkout page redirect URL
$checkoutPageService = CheckoutPageService::create($environment);
$redirectUrl = $checkoutPageService->getCheckoutPageUrl($checkoutPageItem);
```
### Handling the checkout page response

After confirming the purchase on the checkout page, the user is redirected back to the shop.
In order to complete the purchase, the payment must be committed via Easypay's REST API. Use
the `CheckoutPageResponseService` to get the `payment-ID` or `subscription-ID` required to 
commit the payment:

```php
use Gridonic\EasyPay\CheckoutPage\CheckoutPageResponse

// Create an instance from the available GET parameters
$checkoutPageResponse = CheckoutPageResponse::createFromGet();

if ($checkoutPageResponse->isSuccess()) {
    $paymentId = $checkoutPageResponse->getPaymentId();
    
    // or if the submitted CheckoutPageItem is a subscription (recurrent service)
    $authSubscriptionId = $checkoutPageResponse->getAuthSubscriptionId();
} else {
    print_r($checkoutPageResponse->getErrorCode())
}
```

### Commit payments

One-time (direct) payments need to be committed via Easypay's REST API.
Use the `RestApiService` to do so:

```php
use Gridonic\EasyPay\REST\RESTApiService;

$restApiService = RESTApiService::create($environment);

// Commit a direct payment
$directPaymentResponse = $restApiService->directPayment('paymentId');

if ($directPaymentResponse->isSuccess()) {
    // Payment commited successfully
} else {
    // A more detailed error is available as error message:
    $errorMessages = $directPaymentResponse->getErrorMessages();
    $errorMessage = array_pop($errorMessages);
    $errorMessage->getMessage();
    $errorMessage->getCode();
    $errorMessage->getField();
    $errorMessage->getRequestId();
}
```

In case of a service subscription, the procedure is similar:

```php
// Authorize a subscription
$authSubscriptionResponse = $restApiService->authorizeSubscription('authSubscriptionId');

if ($authSubscriptionResponse->isSuccess()) {
    // Subscription authorized successfully
} else {
    $errorMessages = $authSubscriptionResponse->getErrorMessages();
    $errorMessage = array_pop($errorMessages);
    // ...accessing the error details is identical to the direct payment example above
}
```

## Easypay REST API

The `RestApiService` class offers an abstraction of Easypay's REST API to manage payments.

---

**`directPayment(string $paymentId, $operation = 'COMMIT') : DirectPaymentResponse`**

Commit/Reject or Refund a direct payment.
* Available operations: `COMMIT`, `REJECT`, or `REFUND`.

---

**`getDirectPayment(string $paymentId) : DirectPaymentResponse`**

Get all information about a direct payment.

---

**`authorizeSubscription(string $authSubscriptionId, $operation = 'COMMIT') : AuthSubscriptionResponse`**

Commit/Reject/Refund/Renew or Cancel an authorized subscription.
* Available operations: `COMMIT`, `REJECT`, `REFUND`, `RENEW` or `CANCEL`.

---

**`getAuthorizeSubscription(string $authSubscriptionId) : AuthSubscriptionResponse`**

Get all information about an authorized subscription.

---

## Run tests

Make sure that the `dev-dependencies` are installed, then execute phpunit from the `vendor` directory:

```
vendor/bin/phpunit tests
```
