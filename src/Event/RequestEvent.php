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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct($request)
    {
        $this->request = $request;

        parent::__construct(array('request' => $request));
    }

    /**
     * @return RequestInterface
     */
    public function getContext()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Constants::OMNIPAY_REQUEST_BEFORE_SEND;
    }
}
