# Data transfer objects with batteries included

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/spatie/data-transfer-object)
[![Build Status](https://img.shields.io/travis/spatie/data-transfer-object/master.svg?style=flat-square)](https://travis-ci.org/spatie/data-transfer-object)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/data-transfer-object.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/data-transfer-object)
[![StyleCI](https://github.styleci.io/repos/153632216/shield?branch=master)](https://github.styleci.io/repos/153632216)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/spatie/data-transfer-object)

## Installation

You can install the package via composer:

```bash
composer require spatie/data-transfer-object
```

## Have you ever…

… worked with an array of data, retrieved from a request, a CSV file or a JSON API; and wondered what was in it?

Here's an example:

```php
public function handleRequest(array $dataFromRequest)
{
    $dataFromRequest[/* what to do now?? */];
}
```

The goal of this package is to structure "unstructured data", which is normally stored in associative arrays.
By structuring this data into an object, we gain several advantages:

- We're able to type hint data transfer objects, instead of just calling them `array`.
- By making all properties on our objects typeable, we're sure that their values are never something we didn't expect.
- Because of typed properties, we can statically analyze them and have auto completion.

Let's look at the example of a JSON API call:

```php
$post = $api->get('posts', 1); 

[
    'title' => '…',
    'body' => '…',
    'author_id' => '…',
]
```

Working with this array is difficult, as we'll always have to refer to the documentation to know what's exactly in it. 
This package allows you to create data transfer object definitions, classes, which will represent the data in a structured way.

We did our best to keep the syntax and overhead as little as possible:

```php
class PostData extends DataTransferObject
{
    /** @var string */
    public $title;
    
    /** @var string */
    public $body;
    
    /** @var \Author */
    public $author;
}
```

An object of `PostData` can from now on be constructed like so:

```php
$postData = new PostData([
    'title' => '…',
    'body' => '…',
    'author_id' => '…',
]);
```

Now you can use this data in a structured way:

```php
$postData->title;
$postData->body;
$postData->author_id;
```

It's, of course, possible to add static constructors to `PostData`:

```php
class PostData extends DataTransferObject
{
    // …
    
    public static function fromRequest(Request $request): self
    {
        return new self([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'author' => Author::find($request->get('author_id')),
        ]);
    }
}
```

By adding doc blocks to our properties, their values will be validated against the given type; 
and a `TypeError` will be thrown if the value doesn't comply with the given type.

Here are the possible ways of declaring types:

```php
class PostData extends DataTransferObject
{
    /**
     * Built in types: 
     *
     * @var string 
     */
    public $property;
    
    /**
     * Classes with their FQCN: 
     *
     * @var \App\Models\Author
     */
    public $property;
    
    /**
     * Lists of types: 
     *
     * @var \App\Models\Author[]
     */
    public $property;
    
    /**
     * Iterator of types: 
     *
     * @var iterator<\App\Models\Author>
     */
    public $property;
    
    /**
     * Union types: 
     *
     * @var string|int
     */
    public $property;
    
    /**
     * Nullable types: 
     *
     * @var string|null
     */
    public $property;
    
    /**
     * Mixed types: 
     *
     * @var mixed|null
     */
    public $property;
    
    /**
     * Any iterator : 
     *
     * @var iterator
     */
    public $property;
    
    /**
     * No type, which allows everything
     */
    public $property;
}
```

When PHP 7.4 introduces typed properties, you'll be able to simply remove the doc blocks and type the properties with the new, built-in syntax.

### Working with collections

If you're working with collections of DTOs, you probably want auto completion and proper type validation on your collections too.
This package adds a simple collection implementation, which you can extend from.

```php
use \Spatie\DataTransferObject\DataTransferObjectCollection;

class PostCollection extends DataTransferObjectCollection
{
    public function current(): PostData
    {
        return parent::current();
    }
}
```

By overriding the `current` method, you'll get auto completion in your IDE, 
and use the collections like so.

```php
foreach ($postCollection as $postData) {
    $postData-> // … your IDE will provide autocompletion.
}

$postCollection[0]-> // … and also here.
```

Of course you're free to implement your own static constructors:

```php
class PostCollection extends DataTransferObjectCollection
{
    public static function create(array $data): PostCollection
    {
        $collection = [];

        foreach ($data as $item)
        {
            $collection[] = PostData::create($item);
        }

        return new self($collection);
    }
}
```

### Automatic casting of nested DTOs

If you've got nested DTO fields, data passed to the parent DTO will automatically be cast.

```php
class PostData extends DataTransferObject
{
    /** @var \AuthorData */
    public $author;
}
```

`PostData` can now be constructed like so:

```php
$postData = new PostData([
    'author' => [
        'name' => 'Foo',
    ],
]);
```

### Automatic casting of nested array DTOs

Similarly to above, nested array DTOs will automatically be cast.

```php
class TagData extends DataTransferObject
{
    /** @var string */
   public $name;
}

class PostData extends DataTransferObject
{
    /** @var \TagData[] */
   public $tags;
}
```

`PostData` will automatically construct tags like such:

```php
$postData = new PostData([
    'tags' => [
        ['name' => 'foo'],
        ['name' => 'bar']
    ]
]);
```
**Attention**: For nested type casting to work your Docblock definition needs to be a Fully Qualified Class Name (`\App\DTOs\TagData[]` instead of `TagData[]` and an use statement at the top)

### Immutability

If you want your data object to be never changeable (this is a good idea in some cases), you can make them immutable:

```php
$postData = PostData::immutable([
    'tags' => [
        ['name' => 'foo'],
        ['name' => 'bar']
    ]
]);
```

Trying to change a property of `$postData` after it's constructed, will result in a `DataTransferObjectError`.

### Helper functions

There are also some helper functions provided for working with multiple properties at once. 

```php
$postData->all();

$postData
    ->only('title', 'body')
    ->toArray();
    
$postData
    ->except('author')
    ->toArray();
``` 

You can also chain these methods:

```php
$postData
    ->except('title')
    ->except('body')
    ->toArray();
```

It's important to note that `except` and `only` are immutable, they won't change the original data transfer object.

### Exception handling

Beside property type validation, you can also be certain that the data transfer object in its whole is always valid.
On constructing a data transfer object, we'll validate whether all required (non-nullable) properties are set. 
If not, a `Spatie\DataTransferObject\DataTransferObjectError` will be thrown.

Likewise, if you're trying to set non-defined properties, you'll get a `DataTransferObjectError`.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## External tools

- [json2dto](https://json2dto.atymic.dev/): a GUI to convert JSON objects to DTO classes

## Credits

- [Brent Roose](https://github.com/brendt)
- [All Contributors](../../contributors)

Our `Arr` class contains functions copied from Laravels `Arr` helper.

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
