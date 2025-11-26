<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Laravel\Traits;

use Ol3x1n\DataTransferObject\Contracts\DTOInterface;

trait HasCastable
{
    public function get($model, string $key, $value, array $attributes): ?static
    {
        if ($value === null) {
            return null;
        }

        $data = is_string($value) ? json_decode($value, true) : $value;

        return new static($data);
    }

    public function set($model, string $key, $value, array $attributes): array
    {
        if ($value instanceof DTOInterface) {
            return [$key => json_encode($value->toArray())];
        }

        return [$key => $value];
    }
}
