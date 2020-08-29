<style>
    .coupon-area-ajax .input-group{
        display: flex;
        width: 350px;
    }
    .coupon-area-ajax .input-group input[type="text"]{
        padding: 0px 10px;
        border-radius: 3px;
        height: 40px !important;
        line-height: 40px !important;
    }
    .coupon-area-ajax .input-group .btn-apply-coupon{
        padding: 0px 20px;
        border: none;
        border-radius: 3px;
        height: 40px;
        line-height: 40px;
        margin-left: 2px;
        background-color: #1e7e34;
        color: #fff;
        white-space: nowrap;
        opacity: 1;
    }
    .coupon-area-ajax .input-group .btn-apply-coupon.active{
        opacity: 0.8;
    }
    .coupon-area-ajax .text-danger{
        color: #dc3545;
    }
    .coupon-area-ajax .text-success{
        color: #17a2b8
    }

</style>

<div class="coupon-area-ajax">
    <div class="input-group">
        <input type="text" name="coupon" id="coupon" placeholder="{!! !empty($placeholder) ? $placeholder : 'Enter coupon code' !!}" class="form-control">
        <input type="hidden" name="ids" id="ids" value="{!! !empty($ids) ? json_encode($ids) : ''!!}">
        <button class="btn-apply-coupon">{{ __('Coupon::field.button') }}</button>
    </div><!--.input-group-->
    <small class="text-danger" style="display: none"></small>
    <small class="text-success" style="display: none"></small>
</div><!--.coupon-area-ajax-->