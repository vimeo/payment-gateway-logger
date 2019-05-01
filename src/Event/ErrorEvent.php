<?php
/**
 * Error event to be used by the payment gateway logger.
 *
 * @package    payment-gateway-logger
 * @author     manzoj
 * @version    1
 */

namespace PaymentGatewayLogger\Event;

use Exception;
use Guzzle\Common\Event;

class ErrorEvent extends Event
{
    /**
     * @param Exception $error
     * @param string    $request_name
     */
    public function __construct($error, $request_name)
    {
        parent::__construct(array('error' => $error, 'request_name' => $request_name));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_REQUEST_ERROR;
    }
}
