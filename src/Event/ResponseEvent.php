<?php
/**
 * Response event to be used by the payment gateway logger.
 *
 * @package    payment-gateway-logger
 * @author     manzoj
 * @version    1
 */

namespace PaymentGatewayLogger\Event;

use Guzzle\Common\Event;
use Omnipay\Common\Message\ResponseInterface;

class ResponseEvent extends Event
{
    /**
     * @param ResponseInterface $response
     * @param string            $request_name
     */
    public function __construct($response, $request_name)
    {
        parent::__construct(array('response' => $response, 'request_name' => $request_name));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_RESPONSE_SUCCESS;
    }
}
