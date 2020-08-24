<?php

namespace Sudo\Coupon\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class CouponSeedCommand extends Command {

	protected $signature = 'sudo/coupon:seeds';

    protected $description = 'Khởi tạo dữ liệu mẫu cho coupon';

    public function handle() {
    }
}