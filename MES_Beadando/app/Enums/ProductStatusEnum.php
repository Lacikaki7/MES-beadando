<?php

namespace App\Enums;

enum OrderStatusEnum : string {
    case PENDING = 'pending';
    case IN_PRODUCTION = 'in_production';
    case PRODUCED = 'produced';
    case DELIVERED = 'delivered';
    
}