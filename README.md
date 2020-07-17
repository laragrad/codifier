# laragrad/codifier

This package provides a service that enable to use any project config data as dictionary of codes with multilanguage support and cache using.

## Installing

Run command in console

	composer require laragrad/codifier

Add next lines into your `config/app.php`

	$providers => [
		...
		\Laragrad\Codifier\CodifierServiceProvider::class,
	],
	
	$aliases => [
		...
		'Codifier' => \Laragrad\Codifier\CodifierServiceFacade::class,
	],

Run command in console

	php artisan vendor:publish
	
and type choise number to publish `\Laragrad\Codifier\CodifierServiceProvider`

Will be published next files:

* `/config/laragrad/codifier/config.php` - Package configuration
* `/config/laragrad/codifier/example.php` - Example codifier data
* `/resources/lang/vendor/laragrad/codifier/en/messages.php` - Package messages
* `/resources/lang/vendor/laragrad/codifier/en/example.php` - Example codifier translations

## Configurating

See package configuration example in file `/config/laragrad/codifier/config.php`.

There are two root elements

* **use_cache** - A boolean option for enable or disable cache using.
* **sections** - An array of codifier section configurations.

### Section configuration

Section configuration must have next options

* **data_path** is a path to config consisted section data
* **trans_base_path** is a base path to the lang translation resource
* **handler** is an array with class and method names of section handler

## Using Codifier facade

### Codifier::get()

To get full codifier data of section add line

	$data = Codifier::get('codifier_example');
	
Put path to concrete element to get concrete element of codifier section data.

	$data = Codifier::get('codifier_example', '1.next');

Third argument is a locale.

	$data = Codifier::get('codifier_example', null, 'ru');





