# Messenger

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Addition to Cronario which has already implemented the main types of Jobs and Workers: **Curl, Sms, Mail, Hipchat**

## Install

Via Composer

``` bash
$ composer require cronario/messenger
```

## Usage


### Examples Curl / Hipchat / Sms / Mail

``` php
// Curl
$ping = new \Messenger\Curl\Job([
    'params'   => [
        'url'        => 'https://example.com',
        'method'     => 'GET',
        'expectCode' => 200,
    ],
    'comment'  => "ping each minute",
    'schedule' => '* * * * *',
    'isSync'   => false,
]);

$result = $ping();

// Hipchat
$hipchat = new \Messenger\Hipchat\Job([
    'params'  => [
        'token'  => 'xxx',
        'room'   => 'MyRoom',
        'from'   => 'Test',
        'msg'    => 'text ...',
    ],
    'comment' => "hipchat message",
    'isSync'  => false,
]);

$result = $hipchat();


// Sms
$sms = new \Messenger\Sms\Job([
    'params' => [
        'recipient' => '380670000000',
        'sender'    => 'SuperCompany',
        'text'      => "Hellow world!",
    ],
    'comment'     => "My first sms",
    'isSync'      => false,
]);

$result = $sms();


// Mail
$mail = new \Messenger\Mail\Job([
    Job::P_PARAMS => [
        'fromMail' => 'boss@example.com',
        'fromName' => 'Big Boss',
        'toMail'   => "person@example.com",
        'subject'  => "Subject ...",
        'body'     => "Body ....",
    ],
    'comment'     => "My first mail",
    'isSync'      => false,
]);

$result = $mail();

```

### Example combine Curl and Sms 

``` php
$ping = new \Messenger\Curl\Job([
    'params'   => [
        'url'        => 'https://example.com',
        'method'     => 'GET',
        'expectCode' => 200,
    ],
    'comment'  => "get something",
    'callback' => [
        'onSuccess' => [
            new \Messenger\Sms\Job([
                'params'   => [
                    'recipient' => '380670000000',
                    'sender'    => 'SuperCompany',
                    'text'      => "Hellow world!",
                ],
                'comment'  => "My callback sms",
                'callback' => [
                    /* ... */
                ]
            ])
        ]
    ]
]);

$ping();
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/cronario/messenger.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cronario/messenger/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cronario/messenger.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cronario/messenger.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cronario/messenger.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/cronario/messenger
[link-travis]: https://travis-ci.org/cronario/messenger
[link-scrutinizer]: https://scrutinizer-ci.com/g/cronario/messenger/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cronario/messenger
[link-downloads]: https://packagist.org/packages/cronario/messenger
[link-author]: https://github.com/vlad-groznov
[link-contributors]: ../../contributors