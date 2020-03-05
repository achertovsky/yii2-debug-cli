# yii2-debug-cli

Description
======

Module to reveal CLI scripts in /debug (if its required)

I hope it will be useful for you. 


Installing
======
The preferred way to install this extension is through composer.

```
{
	"require": {
	    "achertovsky/yii2-debug-cli": "@dev"
    }
}
```

or

```
	composer require achertovsky/yii2-debug-cli "@dev"
```

Usage
======

to start using it - please, add it to your modules section

for example: 
```
'debug' => [
    'class' => 'achertovsky\debug\Module',
    'logTarget' => 'achertovsky\debug\LogTarget',
],
```
in advanced template *personally me* use it under common/config/main.php

in basic template i would (never did) put it both to config/web.php and config/console.php (waiting for feedbacks)

Usage of errors hub
======
1. migrate
 `./yii migrate/up -p vendor/achertovsky/yii2-debug-cli/migrations`
2. to cover all entrypoints suggestion is to define dispatcher in main config
- in advanced template *personally me* use it under common/config/main.php
- in basic template i would (never did) put it both to config/web.php and config/console.php (waiting for feedbacks)
```
'components' => [
    'log' => [
        'class' => '\achertovsky\debug\log\Dispatcher',
        //...
    ],
    //...
]
```
3. Use it. /debug/error-hub
