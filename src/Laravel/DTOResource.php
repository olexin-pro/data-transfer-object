<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Laravel;

use Illuminate\Http\Resources\Json\JsonResource;

final class DTOResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
