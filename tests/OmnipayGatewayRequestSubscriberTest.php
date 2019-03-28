<?php

namespace PaymentGatewayLogger;

use Mockery;
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use PaymentGatewayLogger\Event\ErrorEvent;
use PaymentGatewayLogger\Event\RequestEvent;
use PaymentGatewayLogger\Event\ResponseEvent;
use PaymentGatewayLogger\Event\Subscriber\OmnipayGatewayRequestSubscriber;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * payment-gateway-logger
 *
 * @package    payment-gateway-logger
 * @author     manzoj
 * @version    1
 */

class OmnipayGatewayRequestSubscriberTest extends TestCase
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var OmnipayGatewayRequestSubscriber
     */
    private $subscriber;

    /**
     * @var TestLogger
     */
    private $logger;

    protected function setUp()
    {
        $httpClient = new Client();
        $this->eventDispatcher = $httpClient->getEventDispatcher();
        $this->logger = new TestLogger();
        $this->subscriber = new OmnipayGatewayRequestSubscriber('test', $this->logger);

        parent::setUp();
    }

    public function providerLoggingEvents()
    {
        $request = Mockery::mock('Omnipay\Common\Message\RequestInterface');
        $response = Mockery::mock('Omnipay\Common\Message\ResponseInterface');
        $exception = Mockery::mock('Exception');

        $requestEvent = new RequestEvent($request);
        $responseEvent = new ResponseEvent($response);
        $errorEvent = new ErrorEvent($exception);

        $requestRecord = array(
            'level' => LogLevel::INFO,
            'message' => 'omnipay_test',
            'context' => $requestEvent->toArray(),
        );
        $responseRecord = array(
            'level' => LogLevel::INFO,
            'message' => 'omnipay_test',
            'context' => $responseEvent->toArray(),
        );
        $errorRecord = array(
            'level' => LogLevel::ERROR,
            'message' => 'omnipay_test',
            'context' => $errorEvent->toArray(),
        );

        return array(
            array($requestEvent->getType(), $requestEvent, $requestRecord),
            array($responseEvent->getType(), $responseEvent, $responseRecord),
            array($errorEvent->getType(), $errorEvent, $errorRecord),
        );
    }


    /**
     * @dataProvider providerLoggingEvents
     *
     * @param string $event_type
     * @param Event $event
     * @param array $record
     */
    public function testLogging($event_type, $event, array $record)
    {
        $this->eventDispatcher->addSubscriber($this->subscriber);
        $this->eventDispatcher->dispatch($event_type, $event);

        if ($record['level'] === LogLevel::INFO) {
            $this->assertTrue($this->logger->hasInfoRecords());
            $this->assertTrue($this->logger->hasInfo($record));
        }
        else if ($record['level'] === LogLevel::ERROR) {
            $this->assertTrue($this->logger->hasErrorRecords());
            $this->assertTrue($this->logger->hasError($record));
        }
    }
}


