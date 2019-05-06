<?php
/**
 * Error event to be used by the payment gateway logger.
 *
 * @package    payment-gateway-logger
 * @version    1
 */

namespace PaymentGatewayLogger\Event;

use Exception;
use Guzzle\Common\Event;
use Omnipay\Common\Message\RequestInterface;

class ErrorEvent extends Event
{
    /**
     * @param Exception $error
     * @param RequestInterface $request
     */
    public function __construct($error, $request)
    {
        parent::__construct(array('error' => $error, 'request' => $request));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_REQUEST_ERROR;
    }
}
