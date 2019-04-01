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
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var string
     */
    protected $type = Constants::OMNIPAY_REQUEST_SUCCESS;

    /**
     * @param ResponseInterface $response
     */
    public function __construct($response)
    {
        $this->response = $response;

        parent::__construct(array('response' => $response));
    }

    /**
     * @return ResponseInterface
     */
    public function getContext()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
