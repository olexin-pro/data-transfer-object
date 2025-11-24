<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final class BooleanConverter implements TypeConverterInterface
{
    public function convert($value): bool
    {
        return in_array($value, $this->getTrueValues(), true);
    }

    private function getTrueValues()
    {
        return \config('dto.converters.types.boolean', [
            '1',
            'true',
            'on',
            1,
            true,
        ]);
    }
}