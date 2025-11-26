# 📘 Data Transfer Object

[![Latest Version on Packagist](https://img.shields.io/packagist/v/olexin-pro/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/olexin-pro/data-transfer-object)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.3-blue?style=flat-square)](https://www.php.net/releases/)
[![License](https://img.shields.io/packagist/l/olexin-pro/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/olexin-pro/data-transfer-object)
[![Tests](https://img.shields.io/github/actions/workflow/status/olexin-pro/data-transfer-object/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/olexin-pro/data-transfer-object/actions)

A powerful, strict, and production-ready DTO layer for PHP/Laravel. 

This package provides automatic type conversion, PHP 8 Attribute mapping, nested DTO support, Laravel Casts, and seamless integration with API Resources. It ensures your data is validated, normalized, and strongly typed from the moment it enters your application.



## ✨ Features

* 🚀 **Auto-Conversion:** Automatically transforms input arrays/JSON into strongly typed objects.
* 🔍 **PHP 8 Attributes:** Declarative field mapping with `#[Field]`.
* 🔄 **Enum Casting:** Strict type enforcement using native Enums.
* 🧩 **Nested DTOs:** Recursively resolves complex data structures.
* ⚙️ **Strict Validation:** Throws errors on type mismatches or missing required fields.
* 📦 **Laravel Integration:** Includes Model Casts and Form Request mapping.
* 🌐 **Resource Pipeline:** Request → DTO → API Resource flow.
* 🪞 **High Performance:** Uses reflection caching to minimize overhead.

---

## Table of Contents

- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Usage Guide](#-usage-guide)
    - [Manual Instantiation](#manual-instantiation)
    - [Nested DTOs](#nested-dtos)
- [Laravel Integration](#-laravel-integration)
    - [Request Injection](#request-injection)
    - [Model Casts](#model-casts)
    - [API Resources](#api-resources)
- [Reference](#-reference)
    - [Supported Types](#supported-types)
- [Testing](#-testing)
- [License](#-license)

---

## 📦 Installation

Requires **PHP 8.3+**

```bash
composer require olexin-pro/data-transfer-object
````

-----

## 🚀 Quick Start

### 1\. Define your DTO

Create a class extending `AbstractDTO` and use attributes to define fields.

```php
namespace App\DTO;

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
    public ?ProfileDTO $profile; // Nested DTO
}
```

### 2\. Use it

```php
$data = [
    'id' => '5',          // Will be cast to int(5)
    'name' => 'Alex',
    'profile' => [        // Will be hydrated into ProfileDTO
        'age' => '30',
        'country' => 'USA'
    ]
];

$dto = new UserDTO($data);

echo $dto->id; // 5 (int)
echo $dto->profile->country; // "USA"
```

-----

## 🎯 Usage Guide

### Manual Instantiation

You can instantiate a DTO with any iterable (array or collection). Keys are automatically normalized from `camelCase` to `snake_case` during processing.

```php
$dto = new UserDTO([
    'id' => 100,
    'name' => 'John Doe'
]);
```

### Nested DTOs

Complex structures are handled automatically. Simply typehint the property with another DTO class and use `TypeEnum::DTO`.

```php
class ProfileDTO extends AbstractDTO
{
    #[Field('age', TypeEnum::INT)]
    public ?int $age;
}

class UserDTO extends AbstractDTO
{
    #[Field('profile', TypeEnum::DTO)]
    public ProfileDTO $profile;
}
```

**Input:**

```json
{
    "profile": { "age": 25 }
}
```

**Result:** `$dto->profile` is an instance of `ProfileDTO`.

-----

## 📥 Laravel Integration

### Request Injection

The package integrates seamlessly with Laravel's Service Container. You can typehint a DTO in your Controller method, and it will automatically hydrate from the current Request (supporting JSON, Form Data, Query Params).

```php
use App\DTO\UserDTO;

class UserController extends Controller 
{
    public function store(UserDTO $dto)
    {
        // $dto is already validated and hydrated from the request
        $user = $this->service->create($dto);
        
        return new UserResource($user);
    }
}
```

Alternatively, use `fromRequest`:

```php
public function update(Request $request)
{
    $dto = UserDTO::fromRequest($request);
}
```

### Model Casts

Store DTOs directly in your database using Laravel's Custom Casts. The DTO is serialized to JSON on save and hydrated back to a DTO on retrieval.

**In your Model:**

```php
use Ol3x1n\DataTransferObject\Laravel\DTOCast;
use App\DTO\ProfileDTO;

class User extends Model
{
    protected $casts = [
        'profile' => ProfileDTO::class, 
    ];
}
```

**Usage:**

```php
$user->profile->age = 31;
$user->save(); // Saved as JSON column
```

### API Resources

DTOs implement `Arrayable`, making them compatible with Laravel API Resources.

```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        // Works whether $this->resource is a Model or a DTO
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}

// Returning a DTO directly
return new UserResource($userDTO);
```

-----

## 🧰 Reference

### Supported Types (`TypeEnum`)

The `#[Field]` attribute requires a type definition from `TypeEnum`.

| Enum Case | Description |
| :--- | :--- |
| `TypeEnum::INT` | Casts to integer |
| `TypeEnum::FLOAT` | Casts to float |
| `TypeEnum::STRING` | Casts to string |
| `TypeEnum::BOOLEAN` | Casts to boolean |
| `TypeEnum::ARRAY` | Casts to array |
| `TypeEnum::DATE` | Handles date strings |
| `TypeEnum::DTO` | Hydrates a nested DTO |
| `TypeEnum::COLLECTION` | Hydrates a collection of DTOs/items |
| `TypeEnum::DYNAMIC` | No casting, keeps original type |

-----

## 🧠 Architecture & Principles

1.  **Immutability:** DTOs are designed to be immutable after instantiation.
2.  **Safety:** Internal fields (`_raw`, etc.) are hidden.
3.  **Normalization:** Input keys are normalized (snake\_case/camelCase handling).
4.  **Performance:** Reflection metadata is cached to ensure production speed.

-----

## 🧪 Testing

Run the full test suite:

```bash
vendor/bin/phpunit
```

-----

## 📝 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
