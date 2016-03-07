# Trait to Manage an Eloquent Model's Related Content

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-relatable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-relatable)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-relatable/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-relatable)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/xxxxxxxxx.svg?style=flat-square)](https://insight.sensiolabs.com/projects/xxxxxxxxx)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-relatable.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-relatable)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-relatable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-relatable)

The `laravel-relatable` package provides a `HasRelatedContent` trait, which allows you to easily relate models to other models of any type.

```php
// The `Post` class uses the `HasRelatedContent` trait
$post = Post::find(1);

$anotherPost = Post::find(2);
$person = Person::find(1);

$post->relate($anotherPost);
$post->relate($person);
```

Afterwards, you can retrieve the post's related content via the `related` accessor.

```php
$related = $post->related;
// => Collection containing `$anotherPost` and `$person`
```

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Install

You can install the package via composer:

```bash
composer require spatie/laravel-relatable
```

In order to publish the migrations and configuration file, you'll need to register the service provider:

```php
// config/app.php
'providers' => [
    // ...
    Spatie\Relatable\RelatableServiceProvider::class,
];
```

If you want to specify a custom table name, you'll need to publish and edit 
the configuration file:

```bash
php artisan vendor:publish --provider="Spatie\Relatable\RelatableServiceProvider" --tag="config"
```

Publishing and running the migrations is mandatory:

```bash
php artisan vendor:publish --provider="Spatie\Relatable\RelatableServiceProvider" --tag="migrations"
php artisan migrate
```

## Usage

After running the migrations, you can start using the package by adding the `HasRelatedContent` trait to your models.

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Relatable\HasRelatedContent;

class Post extends Model
{
    use HasRelatedContent;
}
```

### Adding and Removing Related Content

You can add related content to a model using the `relate` function. `relate` expects a model or an ID and type as parameters.

```php
$post->relate($anotherPost);
$post->relate($anotherPost->id, Post::class);
```

Removing related content happens with the `unrelate` function, which expects the same parameters.

```php
$post->unrelate($anotherPost);
$post->unrelate($anotherPost->id, Post::class);
```

### Synchronizing Related Content

Related content can be synced like Laravel's sync function for many-to-many relationships. The first parameter of `syncRelated` should be a collection of Eloquent models or an array containing associated arrays with ID's and types.

```php
// Relate all magic posts
$post->syncRelated(Post::where('magic', true)->get());

// Relate post #1
$post->syncRelated([['id' => 1, 'type' => Post::class]]);
```

By default, `syncRelated` will detach all other related models. If you just want to add related content, set the `detach` parameter to false.

```php
// Relate all magic posts, without detaching other related content
$post->syncRelated(Post::where('magic', true)->get());
```

### Retrieving Related Content

The `HasRelatetContent` trait provides an accessor for `related`. Related content will be loaded and cached in memory the first time this function is called.

```php
$post->related; // : \Illuminate\Support\Collection
```

The related content can be manually reloaded via the `loadRelated` method. This method will refill the related cache, and return the collection.

```php
$post->loadRelated(); // : \Illuminate\Support\Collection
```

A `hasRelated` helper function is also provided.

```php
$post->hasRelated(); // : bool
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
