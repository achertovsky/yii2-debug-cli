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

fox example: 
```
 	'debug' => [
            'class' => 'achertovsky\yii2-debug-cli\Module',
            'logTarget' => 'achertovsky\yii2-debug-cli\LogTarget',
        ],
```
