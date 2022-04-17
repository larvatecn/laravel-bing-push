# laravel-bing-push
Bing 自动推送

[![Packagist](https://img.shields.io/packagist/l/larva/laravel-bing-push.svg?maxAge=2592000)](https://packagist.org/packages/larva/laravel-bing-push)
[![Total Downloads](https://img.shields.io/packagist/dt/larva/laravel-bing-push.svg?style=flat-square)](https://packagist.org/packages/larva/laravel-bing-push)


## Installation

```bash
composer require larva/laravel-bing-push -vv
```

## Config

```php
//add services.php
    'bing'=>[
        //bing站长平台
        'queue' => '',//处理推送任务的队列
        'site_token' => '',//网站Token
    ]
```

## 使用
```php
\Larva\Bing\Push\BingPush::push('https://www.aa.com/aaa.html');
```