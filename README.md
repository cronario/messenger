# Messenger

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Addition to Cronario which has already implemented the main types of Jobs and Workers:
- Curl
- Sms
- Mail
- Hipchat

## Install

Via Composer

``` bash
$ composer require croanrio/messenger
```

## Usage

### Curl

``` php
// asynchronous background ping each minute
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
/*
	$result = ['...'];
*/
```

``` php
// simple synchronous request

$job = new \Messenger\Curl\Job([
    'params'  => [
        'url'    => 'https://example.com',
        'method' => 'GET',
    ],
    'comment' => "...",
    'isSync'  => true,
]);

$result = $job();

/*
	$result = ['...'];
*/
```
### Hipchat


``` php
// asynchronous send
$hipchat = new Job([
    'params'  => [
        'token'  => 'xxx',
        'room'   => 'MyRoom',
        'from'   => 'Test',
        'msg'    => 'random',
        'colour' => 'text',
        'format' => "Message super text ...",
    ],
    'comment' => "ping each minute",
    'isSync'  => false,
]);

$result = $hipchat();

/*
	$result = ['...'];
*/
```


### Sms


``` php
// asynchronous send
$sms = new Job([
    'params' => [
        'recipient' => '380670000000',
        'sender'    => 'SuperCompany',
        'text'      => "Hi Vlad!",
    ],
    'comment'     => "my async sms",
    'isSync'      => false,
]);

$result = $sms();

/*
    $result = ['...'];
*/
```

### Mail


``` php
// asynchronous send
$mail = new Job([
    Job::P_PARAMS => [
        'fromMail' => 'boss@example.com',
        'fromName' => 'Big Boss',
        'toMail'   => "person@example.com",
        'subject'  => "Subject ...",
        'body'     => "Body ....",
    ],
    'comment'     => "my async sms",
    'isSync'      => false,
]);
$result = $mail();

/*
    $result = ['...'];
*/
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Vlad Groznov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/league/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/league/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/league/:package_name
[link-travis]: https://travis-ci.org/thephpleague/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/:package_name
[link-downloads]: https://packagist.org/packages/league/:package_name
[link-author]: https://github.com/vlad-groznov
[link-contributors]: ../../contributors

