<img src="https://images.ukfast.co.uk/logos/ukfast/441x126_transparent_strapline.png" alt="UKFast Logo" width="350px" height="auto" />

# Laravel Data Docs

An extension to Spatie's laravel-data package which generates OpenAPI specs for quick and easy documentation of your REST API.

The format of the generated documentation is very flexible and can be easy redefined to suit your request/response structure.

## Installation

First, use composer to require the package as below:

```
composer require ukfast/laravel-data-docs
```

Then all we need to do is to register the service provider in the `providers` key in `config/app.php`:

```
UKFast\LaravelDataDocs\DataDocsServiceProvider::class,
```

## Usage

Documentation is done through PHP8 attributes. You add endpoint definitions to your controllers and point them to your data classes.

The strictly typed nature of the data classes means properties can be reflected and automatically documented.

Out of the box, data-docs supports 5 basic endpoint types and conforms to laravel defaults:

### Index

The index endpoint is for endpoints that return a paginated list, for example:

```
use UKFast\LaravelDataDocs\Endpoints;
use App\Data\PetData;
use App\Models\Pet;

class PetController
{
    #[Endpoints\Index(PetData::class)]
    public function index()
    {
        return PetData::collection(Pet::paginate());
    }
}
```

### Create

The create endpoint is for endpoints that create a new resource.

```
class PetController
{
    #[Endpoints\Create(PetData::class)
    public function store()
    {
    }
}
```

### Show

The show endpoint shows an individual resource

```
class PetController
{
    #[Endpoints\Show(PetData::class)
    public function show()
    {
    }
}
```

### Update

The update endpoint updates a resource

```
class PetController
{
    #[Endpoints\Update(PetData::class)
    public function update()
    {
    }
}
```

### Destroy

The destroy endpoint deletes a resource

```
class PetController
{
    #[Endpoints\Destroy(PetData::class)
    public function destroy()
    {
    }
}
```

## Customising Request and Response Structure

Your API likely has its own format different to the defaults provided by this package. But redefining these is not difficult.

Here's an example of a custom index endpoint:

```
namespace App\Docs;

use UKFast\LaravelDataDocs\Endpoint;
use Attribute;

#[Attribute]
class Index extends Endpoint
{
    public function response()
    {
        return [
            'data' => [$this->ref($this->args[0])],
            'meta' => [
                'per_page' => 15,
                'total_pages' => 10,
                'current_page' => 1,
            ],
        ];
    }


    public function dataObjects()
    {
        return $this->args[0];
    }
}
```

Endpoint classes can define two methods: `request` and `response` each return a PHP array outlining the request structure.

Calls to `$this->ref` can be passed a class path to a Data class.

`$this->args` is an array of arguments passed to your attribute, for example `#[MyEndpoint(1, 2, 3)]`, `$this->args` would be `[1,2,3]`

For more examples look inside the `src/Endpoints` folderj

## Contributing

We welcome contributions to this package that will be beneficial to the community.

You can reach out to our open-source team via **open-source@ukfast.co.uk** who will get back to you as soon as possible.

Please refer to our [CONTRIBUTING](CONTRIBUTING.md) file for more information.


## Security

If you think you have identified a security vulnerability, please contact our team via **security@ukfast.co.uk** who will get back to you as soon as possible, rather than using the issue tracker.


## Licence

This project is licenced under the MIT Licence (MIT). Please see the [Licence](LICENCE) file for more information.
