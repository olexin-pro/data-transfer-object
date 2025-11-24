<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Laravel;

use Ol3x1n\DataTransferObject\Contracts\DTOInterface;

final readonly class DTOCast
{
    /**
     * @param class-string<DTOInterface> $dtoClass
     */
    public function __construct(
        private string $dtoClass
    ) {}

    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        $data = is_string($value) ? json_decode($value, true) : $value;

        return new $this->dtoClass($data);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof DTOInterface) {
            return [$key => json_encode($value->toArray())];
        }

        return [$key => $value];
    }
}
