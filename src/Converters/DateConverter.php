<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final class DateConverter implements TypeConverterInterface
{
    public function convert($value): CarbonInterface|null
    {
        if (blank($value)){
            return null;
        }

        return Date::parse($value);
    }
}