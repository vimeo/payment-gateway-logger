Logging capabilities for Omnipay gateways via an `EventSubscriberInterface` subscriber for Omnipay payment gateway-specific events.
These events are dispatched via the HTTP client's `EventDispatcherInterface`.
* The omnipay gateway needs to be updated to emit any of the `RequestEvent`, `ResponseEvent` or `ErrorEvent` objects.
For example in the gateway's `sendData()` methods we can do:

    ```PHP
    $request_name; // The name of the API endpoint being called
    $event_dispatcher = $this->httpClient->getEventDispatcher();
    $event_dispatcher->dispatch(Constants::OMNIPAY_REQUEST_BEFORE_SEND, new RequestEvent($request, $request_name));
    ```

    Logging Errors and Responses events can be emitted like so
    ```PHP
    $event_dispatcher->dispatch(Constants::OMNIPAY_REQUEST_ERROR new ErrorEvent($exception', $request_name));
    $event_dispatcher->dispatch(Constants::OMNIPAY_RESPONSE_SUCCESS, new ResponseEvent($response, $request_name));
    ```

`OmnipayGatewayRequestSubscriber.php` takes in a logger of type `LoggerInterface` which will listen to and log these events.

The subscriber can be set up  to listen to these events when instantiating the HTTP client for the gateway like so:

```PHP
$httpClient = new GuzzleClient();
$gateway = Omnipay::create('Vindicia', $httpClient);
$eventDispatcher = $httpClient->getEventDispatcher();
$eventDispatcher->addSubscriber(new OmnipayGatewayRequestSubscriber($gateway_name, new LoggerClassThatImplementsPSRInterface()));
```
