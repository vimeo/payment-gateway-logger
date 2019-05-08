<?php
/**
 * payment-gateway-logger
 *
 * @package    payment-gateway-logger
 * @version    1
 */

namespace PaymentGatewayLogger\Event\Subscriber;

use Exception;
use Guzzle\Common\Event;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use PaymentGatewayLogger\Event\Constants;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OmnipayGatewayRequestSubscriber implements EventSubscriberInterface
{
    const PRIORITY = 0;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $gateway_name;

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
     *         'gateway_name' => The name of the gateway being logged,
     *         'request_name' => The name of the request object,
     *         'data' => The raw data associated with the request.
     *     )
     * @param Event $event
     * @return void
     */
    public function onOmnipayRequestBeforeSend(Event $event)
    {
        $request_event = $event->toArray();

        /** @var RequestInterface $request */
        $request = $request_event['request'];

        $context = array(
            'gateway_name' => $this->gateway_name,
            'request_name' => strtolower(get_class($request)),
            'data' => $request->getData()
        );

        $this->logger->info(Constants::OMNIPAY_REQUEST_BEFORE_SEND, $context);
    }

    /**
     * Triggers a log write when a request completes.
     *
     * The event will be converted to an array before being logged. It will contain the following properties:
     *     array(
     *         'gateway_name' => The name of the gateway being logged,
     *         'request_name' => The name of the request object,
     *         'is_successful' => True if the response received a successful HTTP response code, false otherwise,
     *         'response_code' => The HTTP response code,
     *         'response_message' => The HTTP response message,
     *         'soap_id' => SOAP id associated with this request (if any),
     *         'transaction_reference' => The gateway transaction id reference
     *     )
     * @param Event $event
     * @return void
     */
    public function onOmnipayResponseSuccess(Event $event)
    {
        $response_event = $event->toArray();

         /** @var ResponseInterface $response */
        $response = $response_event['response'];

        $is_successful = $response->isSuccessful();
        $response_code = $response->getCode() ?: null;

        $context = array(
            'gateway_name' => $this->gateway_name,
            'request_name' => strtolower(get_class($response->getRequest())),
            'is_successful' => $is_successful,
            'response_code' => $response_code,
            'response_message' => $response->getMessage(),
            'soap_id' => method_exists($response, 'getSoapId') ? $response->getSoapId() : null,
            'transaction_reference' => $response->getTransactionReference()
        );

        $this->logger->notice(Constants::OMNIPAY_RESPONSE_SUCCESS, $context);
    }

    /**
     * Triggers a log write when a request fails.
     *
     * The event will be converted to an array before being logged. It will contain the following properties:
     *     array(
     *         'gateway_name' => The name of the gateway being logged,
     *         'request_name' => The name of the request object,
     *         'error_code' => The exception error code,
     *         'error_message' => The exception error message,
     *         'trace' => The exception trace string
     *     )
     * @param Event $event
     * @return void
     */
    public function onOmnipayRequestError(Event $event)
    {
        $error_event = $event->toArray();

        /** @var Exception $error */
        $error = $error_event['error'];

        /** @var RequestInterface $request */
        $request = $error_event['request'];

        $context = array(
            'gateway_name' => $this->gateway_name,
            'request_name' => strtolower(get_class($request)),
            'error_code' => $error->getCode(),
            'error_message' => $error->getMessage(),
            'trace' => $error->getTraceAsString()
        );

        $this->logger->error(Constants::OMNIPAY_REQUEST_ERROR, $context);
    }
}
