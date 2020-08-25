<?php

namespace Sudo\Coupon\Models;

use Sudo\Base\Models\BaseModel;

class Coupon extends BaseModel
{
    protected $table = 'coupons';

    protected $fillable = [
        'id',
        'name',
        'code',
        'type',
        'value',
        'max_value',
        'select',
        'limit',
        'used',
        'start_time',
        'end_time',
        'status',
        'created_at',
        'updated_at'
    ];

    public function couponSelect()
    {
        return $this->hasMany(\Sudo\Coupon\Models\CouponSelect::class, 'coupon_id', 'id');
    }

    public function updateQuantity()
    {
        try{
            $this->increment('used');
            return ['status' => true, 'message' => 'Success!'];
        }catch (\Exception $e){
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
