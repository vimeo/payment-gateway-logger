<?php

namespace PaymentGatewayLogger;

use Exception;
use Guzzle\Common\Event;
use InvalidArgumentException;
use Guzzle\Http\Client;
use Mockery;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use PaymentGatewayLogger\Event\ErrorEvent;
use PaymentGatewayLogger\Event\RequestEvent;
use PaymentGatewayLogger\Event\ResponseEvent;
use PaymentGatewayLogger\Event\Subscriber\OmnipayGatewayRequestSubscriber;
use PaymentGatewayLogger\Test\Framework\TestLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * payment-gateway-logger
 *
 * @package    payment-gateway-logger
 * @version    1
 */

class OmnipayGatewayRequestSubscriberTest extends TestCase
{
    /**
     * @var EventDispatcherInterface
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

    /**
     * @return void
     */
    protected function setUp()
    {
        $httpClient = new Client();
        $this->eventDispatcher = $httpClient->getEventDispatcher();
        $this->logger = new TestLogger();
        $this->subscriber = new OmnipayGatewayRequestSubscriber('test', $this->logger);
        parent::setUp();
    }

    /**
     * @return array
     */
    public function providerLoggingEvents()
    {
        /** @var RequestInterface $request */
        $request = Mockery::mock('Omnipay\Common\Message\RequestInterface');

        /** @var ResponseInterface $response */
        $response = Mockery::mock('Omnipay\Common\Message\ResponseInterface');

        /** @var Exception $exception */
        $exception = Mockery::mock('Exception');

        $requestEvent = new RequestEvent($request);
        $responseEvent = new ResponseEvent($response);
        $errorEvent = new ErrorEvent($exception, $request);

        $requestRecord = array(
            'level' => LogLevel::INFO,
            'message' => 'omnipay_test',
            'context' => $requestEvent->toArray(),
        );
        $responseRecord = array(
            'level' => LogLevel::NOTICE,
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
     *
     * @return void
     */
    public function testLogging($event_type, $event, array $record)
    {
        $this->eventDispatcher->addSubscriber($this->subscriber);
        $this->eventDispatcher->dispatch($event_type, $event);

        $context = $event->toArray();

        if ($record['level'] === LogLevel::INFO) {
            $this->assertInstanceOf('Omnipay\Common\Message\RequestInterface', $context['request']);
            $this->assertTrue($this->logger->hasInfoRecords());
            $this->assertTrue($this->logger->hasInfo($record));
        } else if ($record['level'] === LogLevel::NOTICE) {
            $this->assertInstanceOf('Omnipay\Common\Message\ResponseInterface', $context['response']);
            $this->assertTrue($this->logger->hasNoticeRecords());
            $this->assertTrue($this->logger->hasNotice($record));
        } else if ($record['level'] === LogLevel::ERROR) {
            $this->assertInstanceOf('\Exception', $context['error']);
            $this->assertTrue($this->logger->hasErrorRecords());
            $this->assertTrue($this->logger->hasError($record));
        } else {
            throw new InvalidArgumentException('Logging level has invalid type');
        }
    }
}
