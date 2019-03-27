<?php

namespace Event;

/**
 * Class events.
 *
 * @package    payment-gateway-logger
 * @author     manzoj
 * @version    1
 */

final class Constants
{
    const OMNIPAY_REQUEST = 'omnipay.request.before_send';
    const OMNIPAY_RESPONSE = 'omnipay.request.sent';
    const OMNIPAY_ERROR = 'omnipay.request.error';
}
