<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Contracts;

interface TypeConverterInterface
{
    public function convert(mixed $value): mixed;
}