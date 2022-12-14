# Dailymotion

Laravel Dailymotion API wrapper

## Installation
```php
composer require bakcay/dailymotion
```

If auto discovery not enabled manually add code in config/app.php

```php
Bakcay\DailyMotion\DailyMotionProvider::class,
```


To generate config file

```php 
php artisan vendor:publish --provider="Bakcay\DailyMotion\DailyMotionProvider"
```


## Usage

To get  video list
```php
$result = DailyMotion::get( 
    '/videos', [
        'fields' => 'id,title,owner'
    ]);
```

To upload file

```php
DailyMotion::file($url)->post('/me/videos',[
    'title'     => 'Dailymotion upload test',
    'tags'      => 'dailymotion,api,sdk,test',
    'channel'   => 'videogames',
    'published' => true
]);
```


[Dailymotion documentation](https://developer.dailymotion.com/)
