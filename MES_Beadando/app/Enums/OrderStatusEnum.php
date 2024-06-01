<?php

namespace App\Enums;

enum OrderStatusEnum : string {
    case PENDING = 'pending';
    case IN_PRODUCTION = 'in_production';
    case COMPLETED = 'completed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case DECLINED = 'declined';
    
}