# php-value filter dsl

> A PHP Library to easily send Facebook Messenger message with the REST Web Services.

[![Total Downloads](https://img.shields.io/packagist/dt/gomoob/php-facebook-messenger.svg?style=flat)](https://packagist.org/packages/gomoob/php-facebook-messenger)
[![Latest Stable Version](https://img.shields.io/packagist/v/gomoob/php-facebook-messenger.svg?style=flat)](https://packagist.org/packages/gomoob/php-facebook-messenger)
[![Build Status](https://img.shields.io/travis/gomoob/php-facebook-messenger.svg?style=flat)](https://travis-ci.org/gomoob/php-facebook-messenger)
[![Coverage](https://img.shields.io/coveralls/gomoob/php-facebook-messenger.svg?style=flat)](https://coveralls.io/r/gomoob/php-facebook-messenger?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/gomoob/php-facebook-messenger.svg?style=flat)](https://codeclimate.com/github/gomoob/php-facebook-messenger)
[![License](https://img.shields.io/packagist/l/gomoob/php-facebook-messenger.svg?style=flat)](https://packagist.org/packages/gomoob/php-facebook-messenger)

## First sample, creating a Facebook Messenger text message

```php
// Create a Facebook Messenger client
$client = Client::create()->setPageAccessToken('XXXX-XXX');

// Create a request to send a simple Text Message
$request = TextMessageRequest::create()
    ->setRecipient(Recipient::create()->setId('USER_ID'))
    ->setMessage(TextMessage::create()->setText('hello, world !'));

// Call the REST Web Service
$response = $client->sendMessage($textMessageRequest);

// Check if its ok
if($response->isOk()) {
    print 'Great, the message has been sent !';
} else {
    print 'Oups, the sent failed :-(';
    print 'Status code : ' . $response->getStatusCode();
    print 'Status message : ' . $response->getStatusMessage();
}
```

Easy, isn't it ?

## Documentation

In progress.

## About Gomoob

At [Gomoob](https://www.gomoob.com) we build high quality software with awesome Open Source frameworks everyday. Would
you like to start your next project with us? That's great! Give us a call or send us an email and we will get back to
you as soon as possible !

You can contact us by email at [contact@gomoob.com](mailto:contact@gomoob.com) or by phone number
[(+33) 6 85 12 81 26](tel:+33685128126) or [(+33) 6 28 35 04 49](tel:+33685128126).

Visit also http://gomoob.github.io to discover more Open Source softwares we develop.
