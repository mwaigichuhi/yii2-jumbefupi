Yii2 JumbeFupi
==============
Yii2 extension for integrating with JumbeFupi SMS Gateway

Requirements
------------

You need to register your account at [JumbeFupi](https://account.jumbefupi.com/site/login) if you want to send messages to your users or customers. Sign up is FREE.

The JumbeFupi Gateway API credentials required (username, API key and senderID) will be available under the Developer page in your control panel.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist danvick/yii2-jumbefupi "*"
```

or add

```
"danvick/yii2-jumbefupi": "*"
```

to the `require` section of your `composer.json` file.

To always use the latest version from Github, in your `composer.json`, add this repository to the `repositories` section.
```json lines
{
  ...
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/danvick/yii2-jumbefupi"
    }
  ],
}
```


Usage
-----

The extension is used as an application component and configured in the application configuration as such:

```php
'components' => [
    ...
    'jumbefupi' => [
        'class' => JumbefupiGateway::class,
        'gatewayUsername' => 'jaribujf',                    // REQUIRED - your JumbeFupi username
        'gatewayApiKey' => 'Jum83fUp14pI_kEy',              // REQUIRED - your JumbeFupi API key
        'senderId' => 'JumbeFupi',                          // REQUIRED - your SenderID / Alphanumeric. If not set here, should be set when sending message
        'callbackUrl' => null,                              // OPTIONAL - the URL where message status response from JumbeFupi Gateway will be sent
        'model' => 'danvick\jumbefupi\models\SmsMessage',   // OPTIONAL - (default: danvick\jumbefupi\models\SmsMessage)
        'db' => 'db',                                       // OPTIONAL - the DB connection component for the messages table
        'cacheBalance' => false,                            // OPTIONAL - whether to store balance after enquiry - cache will be burst on message sending
        'cache' => 'cache',                                 // OPTIONAL - the cache component to store balance if cacheBalance is true 
        'balanceCacheKey' => 'JUMBEFUPI_BALANCE',           // OPTIONAL - cache key for storage of JumbeFupi account balance
        'useFileTransport' => false,                        // OPTIONAL - useful in local development
        'fileTransportPath' => '@runtime/messages'          // OPTIONAL - required if `useFileTransport` is true
    ],
]
```

You also should configure the extension migrations to be run in your application config by adding  `danvick\jumbefupi\migrations` to your `migrationNamespaces`:
```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        ...
        'migrationNamespaces' => [
            ...
            'danvick\jumbefupi\migrations'
        ],
    ],
],
```

### Sending a message

> Note: `yii2-jumbefupi` extension uses the `yii2-httpclient` and 'baseUrl' is already set in the yii2-jumbefupi extension itself. The current API base URL is `https://api.jumbefupi.com/v2`

```php
use danvick\jumbefupi\TextMessage;
...
Yii::$app->jumbefupi->send(new TextMessage(['recipients' => $recipients, 'text' => $message]))
```

`$recipients`: A string of comma separated phone numbers or an array of phone number string

`$message`: Text message to send

`$senderId` (OPTIONAL): The alphanumeric SenderId shown as message sender to user. Optional if already set in `jumbefupi` component in config

### Handling status callbacks / Check message status

You can either create an endpoint to handle message status callback so that when the message status changes (e.g. when message is delivered or otherwise) or manually calling `getMessageStatus()` function

1. Setting up a callback

    Set up an action to handle callbacks from the gateway then update the message status in DB. It's important that this action be unauthenticated
    
    The server response to the callback will take the following shape:
    ```json
    {
        "message_id": "517E70B0F3A611EC84223E99B9487780",
        "phone_number": "0700000000",
        "cost": 1.0,
        "status": "queued",
        "status_message": "Added to queue",
        "sms_count": 1
    }
    ```

2. Manually calling `getMessageStatus()` to get message status

    ```php
    Yii::$app->jumbefupi->getMessageStatus($messageId)
    ```
    `$messageId`: message identifier found within the `message_id` column in the `sms_message` table
    
    The message will be automatically updated in the DB on successful server response.

### Checking your JumbeFupi account balance

Returns your JumbeFupi account balance. The balance can be cached if `cacheBalance` is set to true in config. The cached balance if any will be deleted on sending message(s)
```php
Yii::$app->jumbefupi->checkBalance($fromCache)
```
`$fromCache`: Boolean to enable getting cached balance if available or to ignore cached value. Only useful when `cacheBalance` is set to true in config 

### API Documentation

See the API documentation for more information here: [JumbeFupi API Docs](https://api.jumbefupi.com/v2/docs)
