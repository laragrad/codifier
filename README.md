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

* **data_path**  - a path to config consisted section data
* **trans_base_path** - a base path to the lang translation resource
* **handler** - handler class name

## Codifier facade methods

### get()

Returns section element or full data.

    get(string $section, string|null $path, string|null $locale) : array 

Arguments:

* **$section** - Section name.
* **$path** - Path to section element. If it is NULL then returns full section data.
* **$locale** - Locale code. If it is NULL then current locale.

### getSection()

Returns one section data.

    getSection(string $section, string|null $locale) : array 

Arguments:

* **$section** - Section name.
* **$locale** - Locale code. If it is NULL then current locale.

### getSections()

Returns array of many of all sections.

    getSections(array|null $sections, string|null $locale) : array 

Arguments:

* **$sections** - Array of section names. If it is NULL then all configured sections.
* **$locale** - Locale code. If it is NULL then current locale.

### cache()

Warm cache keys if cache using enabled in configuration.

    cache(array|string|null $sections, array|string|null $locales) : array 

Arguments:

* **$sections** - One section name or array of section names. If it is NULL then all configured sections.
* **$locales** - One locale code or array of locale codes. If it is NULL then all configured locales.

Returns list of messages.

### clear()

Clear cache keys if cache using enabled in configuration.

    clear(array|string|null $sections, array|string|null $locales) : array 

Arguments:

* **$sections** - One section name or array of section names. If it is NULL then all configured sections.
* **$locales** - One locale code or array of locale codes. If it is NULL then all configured locales.

Returns list of messages.

