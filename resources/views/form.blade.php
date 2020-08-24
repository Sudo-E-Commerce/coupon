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
    }
    .coupon-area-ajax .input-group input[type="text"].error{
        border-color: #dc3545 !important;
    }
    .coupon-area-ajax .text-danger{
        color: #dc3545;
    }
    .coupon-area-ajax .text-success{
        color: #17a2b8
    }
    .coupon-area-ajax .input-group .btn-apply-coupon i{
        visibility: hidden;
        margin-left: -10px;
        opacity: 0;
        -webkit-animation: rotation 2s infinite linear;
        -moz--animation: rotation 2s infinite linear;
        -o-animation: rotation 2s infinite linear;
        -ms-animation: rotation 2s infinite linear;
        animation: rotation 2s infinite linear;
        transition: all ease-in-out 0.2s;
        -moz-transition: all ease-in-out 0.2s;
        -o-transition: all ease-in-out 0.2s;
        -webkit-transition: all ease-in-out 0.2s;
        -ms-transition: all ease-in-out 0.2s;

    }
    @-webkit-keyframes rotation {
        from {
            -webkit-transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
        }
    }
    .coupon-area-ajax .input-group .btn-apply-coupon.open i{
        visibility: visible;
        opacity: 1;
        margin-left: 10px;
        position: relative;
        transition: all ease-in-out 0.2s;
        -moz-transition: all ease-in-out 0.2s;
        -o-transition: all ease-in-out 0.2s;
        -webkit-transition: all ease-in-out 0.2s;
        -ms-transition: all ease-in-out 0.2s;
    }
</style>

<div class="coupon-area-ajax">
    <div class="input-group">
        <input type="text" name="coupon" id="coupon" placeholder="{!! !empty($placeholder) ? $placeholder : 'Enter coupon code' !!}" class="form-control">
        <input type="hidden" name="ids" id="ids" value="{!! !empty($ids) ? json_encode($ids) : ''!!}">
        <button class="btn-apply-coupon">{{ __('Coupon::field.button') }}<i class="fa fa-cog" aria-hidden="true"></i></button>
    </div><!--.input-group-->
    <small class="text-danger" style="display: none"></small>
    <small class="text-success" style="display: none"></small>
</div><!--.coupon-area-ajax-->

<script>
    $(document).ready(function () {
        $('.btn-apply-coupon').on('click', function (e) {
            e.preventDefault();
            var _this = $(this);
            _this.addClass('open');

            var couponArea = _this.parents('.coupon-area-ajax');
            var coupon = couponArea.find('#coupon').val();
            var ids = couponArea.find('#ids').val();

            $.ajax({
                type: 'POST',
                url: '{!! route('coupons.getPriceAfterCoupon') !!}',
                data: {
                    _token: '<?php echo csrf_token() ?>',
                    coupon: coupon,
                    ids: ids
                },
                success: function (result) {
                    console.log(result);

                    removeIcon(_this);
                    showMessage(couponArea);

                    if(result.status){
                        couponArea.find('.text-success').text(result.message);
                    } else {
                        couponArea.find('.text-danger').text(result.message);
                    }
                },
                error: function (e) {
                    console.log(e);

                    removeIcon(_this);
                    showMessage(couponArea);
                    couponArea.find('.text-danger').text(e.responseText);
                }
            });
        })

        function removeIcon(_this)
        {
            _this.removeClass('open');
        }

        function showMessage(couponArea)
        {
            couponArea.find('small').text('');
            couponArea.find('small').show();
        }
    })
</script>