# SmsNotify
[![Release](https://img.shields.io/badge/Release-v0.0.2--alpha-yellow?style=flat-square)](https://github.com/RomanStruk/SmsNotify/releases)

Бібліотека для Laravel який реалізує відправку смс повідомлень різними клієнтами по одному API
Підтримуються сторонні сервіси такі як :
* Viber UA
* MTS Communicator BY
* TurboSMS UA

## Встановлення

Рекомендований спосіб встановлення SmsNotify через
[Composer](https://getcomposer.org/).

```bash
composer require romanstruk/smsnotify
```

## Налаштування
```bash
php artisan vendor:publish SmsNotifyServiceProvider
```
Після цього оновіть ```config/smsnotify.php``` вашими налаштуваннями.

## Використання
Щоб використовувати бібліотеку SmsNotify, ви можете використовувати фасад або отримати екземпляр із сервіс контейнера:

```php
SmsNotifyFacade::to(new PhoneNumber('0666000000', 'UA'))
                ->send(new SmsMessage('Some sms text'));
```
або
```php
$smsNotify = app(SmsNotifyInterface::class);
$smsNotify->to(new PhoneNumber('0666000000', 'UA'))
          ->send(new SmsMessage('Some sms text'));
```
Якщо сервіс відправки смс потрібно змінювати динамічно під час виконання то можна використати ```Closure``` для методу ```clientMap(Closure $func)``` результатом має бути один із ключів за яким прікріплений клієнт в файлі конфігурації
```php
SmsNotifyFacade::clientMap(function (){
                    return 'ua';
                })
                ->to(new PhoneNumber('0666000000', 'UA'))
                ->send(new SmsMessage('Some text'));
```
Файл ```config/smsnotify.php```
```php
...
'map' => [
    'ua' => 'log',
    'by' => 'mts-communicator'
]
...
```
