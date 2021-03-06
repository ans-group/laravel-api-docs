<img src="https://images.ukfast.co.uk/logos/ukfast/441x126_transparent_strapline.png" alt="UKFast Logo" width="350px" height="auto" />

# Laravel API Docs [Beta]

*Package is still in beta and is not ready for production use*

Automatically generate API documentation for your laravel API's based off your application routes and handy PHP 8 attributes.

## Installation

First, use composer to require the package as below:

```
composer require ukfast/laravel-api-docs
```

Then all we need to do is to register the service provider in the `providers` key in `config/app.php`:

```
UKFast\LaravelApiDocs\ServiceProvider::class,
```

## Usage

Documentation is generated by scanning your routes file and finding controller methods with special endpoint attributes tagged to them.

Whilst you can easily define your own endpoint types, the package comes with some sensible defaults that generally conform to laravel defaults

### Index

The index endpoint is for endpoints that return a paginated list, for example:

```php
use UKFast\LaravelApiDocs\Endpoints;
use App\Http\Resources\PetResource;
use App\Models\Pet;

class PetController
{
    #[Endpoints\Index(PetResource::class)]
    public function index()
    {
        return PetResource::collection(Pet::paginate());
    }
}
```

### Create

The create endpoint is for endpoints that create a new resource.

```php
use UKFast\LaravelApiDocs\Endpoints;
use App\Http\Resources\PetResource;
use App\Models\Pet;

class PetController
{
    #[Endpoints\Create(PetResource::class)
    public function store()
    {
    }
}
```

### Show

The show endpoint shows an individual resource

```php
use UKFast\LaravelApiDocs\Endpoints;
use App\Http\Resources\PetResource;
use App\Models\Pet;

class PetController
{
    #[Endpoints\Show(PetResource::class)
    public function show()
    {
    }
}
```

### Update

The update endpoint updates a resource

```php
use UKFast\LaravelApiDocs\Endpoints;
use App\Http\Resources\PetResource;
use App\Models\Pet;

class PetController
{
    #[Endpoints\Update(PetResource::class)
    public function update()
    {
    }
}
```

### Destroy

The destroy endpoint deletes a resource

```php
use UKFast\LaravelApiDocs\Endpoints;

class PetController
{
    #[Endpoints\Destroy]
    public function destroy()
    {
    }
}
```

## Customising Request and Response Structure

Your API likely has its own format different to the defaults provided by this package. But redefining these is not difficult.

Here's an example of a custom index endpoint:

```php
namespace App\Docs;

use UKFast\LaravelApiDocs\Endpoint;
use Attribute;

#[Attribute]
class Index extends Endpoint
{
    public function __construct(protected $resource)
    {}

    public function response()
    {
        return [
            'data' => [$this->ref($this->resource)],
            'meta' => [
                'per_page' => 15,
                'total_pages' => 10,
                'current_page' => 1,
            ],
        ];
    }
}
```

Endpoint classes can define two methods: `request` and `response` each return a PHP array outlining the request structure.

Calls to `$this->ref` can be passed a class path to a any class with the #[Resource] attribute on it.

For more examples look inside the `src/Endpoints` folder

## Contributing

We welcome contributions to this package that will be beneficial to the community.

You can reach out to our open-source team via **open-source@ukfast.co.uk** who will get back to you as soon as possible.

Please refer to our [CONTRIBUTING](CONTRIBUTING.md) file for more information.


## Security

If you think you have identified a security vulnerability, please contact our team via **security@ukfast.co.uk** who will get back to you as soon as possible, rather than using the issue tracker.


## Licence

This project is licenced under the MIT Licence (MIT). Please see the [Licence](LICENCE) file for more information.
