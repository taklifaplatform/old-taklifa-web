<?php

namespace Webkul\Marketplace\Enum;

enum Product
{
    case OWNER_YES;
    case OWNER_NO;
    case APPROVED;
    case DISAPPROVED;
    case STATUS_ACTIVE;
    case STATUS_INACTIVE;

    public function value(): int
    {
        return match ($this) {
            Product::OWNER_YES, Product::APPROVED, Product::STATUS_ACTIVE => 1,
            Product::OWNER_NO, Product::DISAPPROVED, Product::STATUS_INACTIVE => 0,
        };
    }
}
