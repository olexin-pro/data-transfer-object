<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Enums;

enum TypeEnum: string
{
    case DYNAMIC = 'dynamic';
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case COLLECTION = 'collection';
    case DATE = 'date';
    case ENUM = 'enum';
    case FLUENT = 'fluent';
}