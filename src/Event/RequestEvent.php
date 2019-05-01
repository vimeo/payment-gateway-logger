<?php
/**
 * Request event to be used by the payment gateway logger.
 *
 * @package    payment-gateway-logger
 * @author     manzoj
 * @version    1
 */

namespace PaymentGatewayLogger\Event;

use Guzzle\Common\Event;
use Omnipay\Common\Message\RequestInterface;

class RequestEvent extends Event
{
    /**
     * @param RequestInterface $request
     * @param string           $request_name
     */
    public function __construct($request, $request_name)
    {
        parent::__construct(array('request' => $request, 'request_name' => $request_name));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_REQUEST_BEFORE_SEND;
    }
}
