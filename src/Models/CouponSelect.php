<?php

namespace Sudo\Coupon\Models;

use Sudo\Base\Models\BaseModel;

class CouponSelect extends BaseModel
{
    protected $table = 'coupon_selects';

    protected $fillable = [
        'id',
        'coupon_id',
        'type_id',
        'created_at',
        'updated_at'
    ];
}
