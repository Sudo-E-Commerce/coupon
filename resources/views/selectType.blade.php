<div class="coupon_type_area">
    <div class="form-group row ">
        <label for="type_id" class="col-lg-12 col-form-label"></label>
        <div class="col-lg-12 col-md-10">
            <select style="width: 100% !important;" class="form-control" name="type_id[]" id="type_id" multiple="multiple"></select>
        </div>
    </div>
    <script>
        // type = 0: all, type = 1: categories, type = 2: products
        $(document).ready(function () {
            generateSelectType();

            $('#type_id').select2();

            $('#select').on('change', function () {
                generateSelectType();
            });
        });

        function generateSelectType() {
            var typeId = $('#select').val();
            var html = '';

            var ids = "{!! !empty($data) ? $data : ''!!}";
            var ids = ids != '' ? JSON.parse(ids) : [];

            $('.coupon_type_area').hide();

            $.ajax({
                type: 'POST',
                url: '{!! route('admin.coupons.getDataAjax') !!}',
                data: {
                    _token: '<?php echo csrf_token() ?>',
                    type_id: typeId
                },
                success: function (result) {
                    var result = JSON.parse(result);


                    $('.coupon_type_area select').html('');

                    if (typeId == 0) {
                        $('.coupon_type_area').hide();
                        return false;
                    } else if(typeId == 2) {
                        $('.coupon_type_area label').text('* Products');
                    } else {
                        $('.coupon_type_area label').text('* Categories');
                    }

                    result.map(function (el, i) {
                        html += '<option value="' + el.id + '"';
                        if (ids.length > 0) {
                            ids.map(function (id, i) {
                                if (id == el.id) {
                                    html += 'selected';
                                }
                            })
                        }
                        html += '>' + el.name + '</option>';
                    });
                    $('.coupon_type_area').show();
                    $('.coupon_type_area select').html(html);
                },
                error: function (e) {
                    alert(e.responseJSON.message);
                }
            });
        }
    </script>
</div>