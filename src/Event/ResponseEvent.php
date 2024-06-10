<?php
/**
 * Response event to be used by the payment gateway logger.
 *
 * @package    payment-gateway-logger
 * @version    1
 */

namespace PaymentGatewayLogger\Event;

use Omnipay\Common\Message\ResponseInterface;

class ResponseEvent extends Event
{
    /**
     * @param ResponseInterface $response
     */
    public function __construct($response)
    {
        parent::__construct(array('response' => $response));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_RESPONSE_SUCCESS;
    }
}
