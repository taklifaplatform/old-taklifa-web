<?php

namespace Webkul\Marketplace\Enum;

enum Seller
{
    case APPROVED;
    case DISAPPROVED;
    case FLAG_REASON_ACTIVE;
    case FLAG_REASON_INACTIVE;

    public function value(): int
    {
        return match ($this) {
            Seller::APPROVED, Seller::FLAG_REASON_ACTIVE      => 1,
            Seller::DISAPPROVED, Seller::FLAG_REASON_INACTIVE => 0,
        };
    }
}
