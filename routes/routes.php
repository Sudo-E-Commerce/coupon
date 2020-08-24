<?php
App::booted(function() {
	$namespace = 'Sudo\Coupon\Http\Controllers';
	
	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function() {
		// Bài viết
        Route::resource('coupons', 'CouponController');
        Route::post('get-data', 'CouponController@getDataAjax')->name('coupons.getDataAjax');
	});

	// Not Auth
	Route::namespace($namespace)->prefix('coupons')->middleware(['web'])->group(function() {
		// lấy giá sản phẩm sau khi sử dụng coupon (single) params: product_id, code of coupon
    	Route::post('price-after-coupon', 'CouponController@getPriceAfterCoupon')->name('coupons.getPriceAfterCoupon');
	});
});