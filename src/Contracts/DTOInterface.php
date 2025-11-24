<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Contracts;

interface DTOInterface
{
    public function __construct(array $data);
    public function getRawData(): array;
}