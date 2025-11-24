# 📘 **Data Transfer Object**

A powerful and strict DTO layer for PHP/Laravel with automatic type conversion, PHP 8 attributes, nested DTO support, Laravel Casts, and seamless integration with API Resources.

Built for production-grade input validation, transformation, and transport.

---

# ✨ Features

* 🚀 Automatic conversion of input into strongly typed DTOs
* 🔍 PHP 8 Attributes for field mapping
* 🔄 Enum-based type casting
* 🧩 Built-in support for nested DTOs
* ⚙️ Strict type validation
* 📦 Laravel Model Casts
* 🌐 Request → DTO → Resource pipeline
* 🪞 Reflection caching for high performance
* 🔒 Safe handling and normalization of input
* 🧪 Includes a full test suite

---

# 📦 Installation

```bash
composer require olexin-pro/data-transfer-object
```

---

# 🚀 Quick Start

Define a DTO:

```php
use Ol3x1n\DataTransferObject\AbstractDTO;
use Ol3x1n\DataTransferObject\Field;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;

class UserDTO extends AbstractDTO
{
    #[Field('id', TypeEnum::INT, required: true)]
    public int $id;

    #[Field('name', TypeEnum::STRING)]
    public ?string $name;

    #[Field('profile', TypeEnum::DTO)]
    public ?ProfileDTO $profile;
}
```

Nested DTO:

```php
class ProfileDTO extends AbstractDTO
{
    #[Field('age', TypeEnum::INT)]
    public ?int $age;

    #[Field('country', TypeEnum::STRING)]
    public ?string $country;
}
```

---

# 🎯 Usage

## Creating DTOs manually

```php
$dto = new UserDTO([
    'id' => '5',
    'name' => 'Alex',
    'profile' => [
        'age' => '30',
        'country' => 'USA'
    ]
]);
```

### Results

* `id` → converted to `int`
* `profile` → automatically converted to `ProfileDTO`
* Input keys are normalized (`camelCase` → `snake_case`)

---

# 📥 Request → DTO

Convert any request into a DTO:

```php
$dto = UserDTO::fromRequest($request);
```

Laravel Controllers can auto-inject DTOs:

```php
class UserController extends Controller 
{
    public function store(CreateUserDTO $dto)
    {
        $user = $this->service->create($dto);
        return new UserResource($user);
    }
    
    public function update(int $id, UpdateUserDTO $dto)
    {
        return new UserResource($this->service->update($id, $dto));
    }
}
```

Works with JSON payloads, POST, GET, form-data, and file uploads.

---

# 🗄 Laravel Model Casts

Store and retrieve DTOs from the database as JSON.

### In a model:

```php
protected $casts = [
    'profile' => ProfileDTO::cast()
];
```

### Usage:

```php
$user->profile->country; // ProfileDTO instance
$user->profile = new ProfileDTO([...]);
$user->save();
```

---

# 🌐 DTO → API Resource

DTOs work seamlessly with Laravel Resources:

```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
```

Usage:

```php
return new UserResource($dto);
// or
return new \Ol3x1n\DataTransferObject\Laravel\DTOResource($dto);
```

---

# 🔄 Nested DTOs

With:

```php
#[Field('profile', TypeEnum::DTO)]
public ProfileDTO $profile;
```

and input:

```json
"profile": { "age": 30, "country": "USA" }
```

A `ProfileDTO` instance is created automatically.

---

# 🧰 Type Enum

```php
enum TypeEnum: string
{
    case INT        = 'int';
    case FLOAT      = 'float';
    case STRING     = 'string';
    case BOOLEAN    = 'boolean';
    case ARRAY      = 'array';
    case DATE       = 'date';
    case DTO        = 'dto';
    case COLLECTION = 'collection';
    case DYNAMIC    = 'dynamic';
}
```

---

# 🧪 Testing

The package ships with complete test coverage.

Run tests:

```bash
vendor/bin/phpunit
```

---

# 🧠 Architecture & Principles

* DTOs are immutable after creation
* Each `Field` attribute defines:

    * input key name
    * conversion type
    * whether the field is required
* Reflection is cached for performance
* Internal DTO fields (`_raw`, `_normalized`) are never exposed
* All fields are strictly type-validated based on PHP property types
* Conversion errors result in `TypeError` or `MissingRequiredField`
* Nested DTOs are automatically resolved

---

# 📚 Advanced DTO example

```php
class OrderDTO extends AbstractDTO
{
    #[Field('id', TypeEnum::INT)]
    public int $id;

    #[Field('items', TypeEnum::ARRAY)]
    public array $items;

    #[Field('customer', TypeEnum::DTO)]
    public CustomerDTO $customer;

    #[Field('total', TypeEnum::FLOAT)]
    public float $total;
}
```

---

# 🧩 Request → DTO → Resource pipeline

```php
public function store(CreateOrderDTO $dto)
{
    $order = $this->service->create($dto);
    return new OrderResource($order);
}
```

---

# 📝 License

MIT License.

---

# 💬 Feedback

Issues and pull requests are welcome.
