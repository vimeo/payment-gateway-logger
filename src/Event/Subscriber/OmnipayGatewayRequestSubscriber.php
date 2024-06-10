<?php
/**
 * payment-gateway-logger
 *
 * @package    payment-gateway-logger
 * @version    1
 */

namespace PaymentGatewayLogger\Event\Subscriber;

use PaymentGatewayLogger\Event\Event;
use PaymentGatewayLogger\Event\Constants;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OmnipayGatewayRequestSubscriber implements EventSubscriberInterface
{
    const PRIORITY = 0;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $gateway_name;

    /**
     * OmnipayGatewayRequestSubscriber constructor.
     *
     * @param string $gateway_name
     * @param LoggerInterface $logger
     */
    public function __construct($gateway_name, $logger)
    {
        $this->logger = $logger;
        $this->gateway_name = 'omnipay_' . $gateway_name;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            Constants::OMNIPAY_REQUEST_BEFORE_SEND  => array('onOmnipayRequestBeforeSend', self::PRIORITY),
            Constants::OMNIPAY_RESPONSE_SUCCESS => array('onOmnipayResponseSuccess', self::PRIORITY),
            Constants::OMNIPAY_REQUEST_ERROR    => array('onOmnipayRequestError', self::PRIORITY),
        );
    }

    /**
     * Triggers a log write before a request is sent.
     *
     * The event will be converted to an array before being logged. It will contain the following properties:
     *     array(
     *         'request' => \Omnipay\Common\Message\AbstractRequest
     *     )
     * @param Event $event
     * @return void
     */
    public function onOmnipayRequestBeforeSend(Event $event)
    {
        $this->logger->info($this->gateway_name, $event->toArray());
    }

    /**
     * Triggers a log write when a request completes.
     *
     * The event will be converted to an array before being logged. It will contain the following properties:
     *     array(
     *         'response' => \Omnipay\Common\Message\AbstractResponse
     *     )
     * @param Event $event
     * @return void
     */
    public function onOmnipayResponseSuccess(Event $event)
    {
        $this->logger->notice($this->gateway_name, $event->toArray());
    }

    /**
     * Triggers a log write when a request fails.
     *
     * The event will be converted to an array before being logged. It will contain the following properties:
     *     array(
     *         'error' => Exception,
     *         'request' => \Omnipay\Common\Message\AbstractRequest
     *     )
     * @param Event $event
     * @return void
     */
    public function onOmnipayRequestError(Event $event)
    {
        $this->logger->error($this->gateway_name, $event->toArray());
    }
}
