<?php
/**
 * Request event to be used by the payment gateway logger.
 *
 * @package    payment-gateway-logger
 * @version    1
 */

namespace PaymentGatewayLogger\Event;

use Guzzle\Common\Event;
use Omnipay\Common\Message\RequestInterface;

class RequestEvent extends Event
{
    /**
     * @param RequestInterface $request
     */
    public function __construct($request)
    {
        parent::__construct(array('request' => $request));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_REQUEST_BEFORE_SEND;
    }
}
