## Mustard Media module

[![StyleCI](https://styleci.io/repos/45991961/shield?style=flat)](https://styleci.io/repos/45991961)
[![Build Status](https://travis-ci.org/hamjoint/mustard-media.svg)](https://travis-ci.org/hamjoint/mustard-media)
[![Total Downloads](https://poser.pugx.org/hamjoint/mustard-media/d/total.svg)](https://packagist.org/packages/hamjoint/mustard-media)
[![Latest Stable Version](https://poser.pugx.org/hamjoint/mustard-media/v/stable.svg)](https://packagist.org/packages/hamjoint/mustard-media)
[![Latest Unstable Version](https://poser.pugx.org/hamjoint/mustard-media/v/unstable.svg)](https://packagist.org/packages/hamjoint/mustard-media)
[![License](https://poser.pugx.org/hamjoint/mustard-media/license.svg)](https://packagist.org/packages/hamjoint/mustard-media)

Photo & video support for [Mustard](http://withmustard.org/), the open source marketplace platform.

### Installation

#### Via Composer (using Packagist)

```sh
composer require hamjoint/mustard-media
```

Then add the Service Provider to config/app.php:

```php
Hamjoint\Mustard\Media\Providers\MustardMediaServiceProvider::class
```

### Licence

Mustard is free and gratis software licensed under the [GPL3 licence](https://www.gnu.org/licenses/gpl-3.0). This allows you to use Mustard for commercial purposes, but any derivative works (adaptations to the code) must also be released under the same licence. Mustard is built upon the [Laravel framework](http://laravel.com), which is licensed under the [MIT licence](http://opensource.org/licenses/MIT).
