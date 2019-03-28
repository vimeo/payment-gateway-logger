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
     * @var Exception
     */
    protected $error;

    /**
     * @var string
     */
    protected $type = Constants::OMNIPAY_ERROR;

    /**
     * @param Exception $error
     */
    public function __construct($error)
    {
        $this->error = $error;

        parent::__construct(array('error' => $error));
    }

    /**
     * @return Exception
     */
    public function getContext()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
