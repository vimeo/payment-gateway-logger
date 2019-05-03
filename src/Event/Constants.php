<?php

namespace PaymentGatewayLogger\Event;

/**
 * Class events.
 *
 * @package    payment-gateway-logger
 * @version    1
 */

final class Constants
{
    const OMNIPAY_REQUEST_BEFORE_SEND = 'omnipay.request.before_send';
    const OMNIPAY_RESPONSE_SUCCESS = 'omnipay.response.success';
    const OMNIPAY_REQUEST_ERROR = 'omnipay.request.error';
}
