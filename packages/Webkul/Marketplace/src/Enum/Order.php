<?php

namespace Webkul\Marketplace\Enum;

enum Order: string
{
    case STATUS_PENDING = 'pending';
    case STATUS_PENDING_PAYMENT = 'pending_payment';
    case STATUS_PROCESSING = 'processing';
    case STATUS_COMPLETED = 'completed';
    case STATUS_CANCELED = 'canceled';
    case STATUS_CLOSED = 'closed';
    case STATUS_FRAUD = 'fraud';
}
