# 📘 **Data Transfer Object**

Конвертация входящих данных через строгие DTO с использованием Reflection, атрибутов PHP 8, автоматического приведения типов, вложенных DTO, Laravel Casts, и интеграции с API Resources.



# ✨ Features

* 🚀 Автоматическое преобразование данных в типизированные DTO
* 🔍 Атрибуты PHP 8 для описания полей
* 🔄 Поддержка enum-типов конвертации
* 🧩 Вложенные DTO
* ⚙️ Строгая валидация типов
* 📦 Поддержка Laravel Model Casts
* 🌐 Request → DTO → Resource pipeline
* 🪞 Reflection cache + высокопроизводительная архитектура
* 🔒 Безопасность и контроль над входящими данными
* 🧪 Unit-тесты из коробки

---

# 📦 Установка

```bash
composer require olexin-pro/data-transfer-object
```


# 🚀 Быстрый старт

Создаём DTO:

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

Вложенный DTO:

```php
class ProfileDTO extends AbstractDTO
{
    #[Field('age', TypeEnum::INT)]
    public ?int $age;

    #[Field('country', TypeEnum::STRING)]
    public ?string $country;
}
```


# 🎯 Использование

## Создание вручную

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

### Результат

* `id` → int
* `profile` → объект `ProfileDTO`
* все поля нормализованы (snake_case → normalized keys)

---

# 📥 Request → DTO

Любой запрос можно преобразовать в DTO:

```php
$dto = UserDTO::fromRequest($request);
```


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

Работает и с JSON, и с POST, и с GET, и с файлами.

---

# 🗄 Laravel Model Casts

Позволяет хранить DTO в базе как JSON и получать обратно объект.

### В модели:

```php
protected $casts = [
    'profile' => ProfileDTO::cast()
];
```

### Использование:

```php
$user->profile->country; // ProfileDTO
$user->profile = new ProfileDTO([...]);
$user->save();
```

---

# 🌐 DTO → API Resource

DTO полностью совместим с Laravel Resources:

```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
```

Использование:

```php
return new UserResource($dto);
// or
return new \Ol3x1n\DataTransferObject\Laravel\DTOResource($dto)
```

---

# 🔄 Вложенные DTO

Если поле помечено:

```php
#[Field('profile', TypeEnum::DTO)]
public ProfileDTO $profile;
```

и в данных:

```json
"profile": { "age": 30, "country": "USA" }
```

→ автоматически создаётся экземпляр ProfileDTO.

---

# 🧰 Enum типов

```php
enum TypeEnum: string
{
    case INT       = 'int';
    case FLOAT     = 'float';
    case STRING    = 'string';
    case BOOLEAN   = 'boolean';
    case ARRAY     = 'array';
    case DATE      = 'date';
    case DTO       = 'dto';
    case COLLECTION= 'collection';
    case DYNAMIC   = 'dynamic';
}
```

# 🧪 Тестирование

Пакет полностью покрыт тестами.

Запуск:

```bash
vendor/bin/phpunit
```


# 🧠 Принципы и архитектура

* DTO неизменяемы после создания
* Каждый Field описывает:
    * имя поля
    * тип конвертации
    * обязательность
* Reflection кэшируется
* Внутренние поля DTO (`_raw`, `_normalized`) скрыты
* Поля приводятся к типам, указанным в PHP
* Все преобразования строго типизированы
* Ошибки приводят к `TypeError` или `MissingRequiredField`


# 📚 Пример продвинутого DTO

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


# 🧩 Маппинг Request → DTO → Resource (Pipeline)

```php
public function store(CreateOrderDTO $dto)
{
    $order = $this->service->create($dto);
    return new OrderResource($order);
}
```

# 📝 Лицензия

MIT License.

# 💬 Обратная связь

Issues и PR приветствуются.
