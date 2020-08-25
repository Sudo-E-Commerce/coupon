<?php

namespace Sudo\Coupon\Http\Controllers;

use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use File;
use ListCategory;
use Illuminate\Http\Response;
use Sudo\Coupon\Models\Coupon;

class CouponController extends AdminController
{
    function __construct()
    {
        $this->models = new \Sudo\Coupon\Models\Coupon;
        $this->table_name = $this->models->getTable();
        $this->module_name = 'Coupons';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests)
    {
        $listdata = new \Sudo\Category\MyClass\ListDataCategory($requests, $this->models, 'Coupon::table.index', $this->has_locale);
        $listdata->search('created_at', 'Ngày tạo', 'range');
        $listdata->search('type', 'Trạng thái', 'array', config('app.status'));
        // Build các button hành động
        $listdata->btnAction('status', 1, __('Table::table.active'), 'success', 'fas fa-edit');
        $listdata->btnAction('status', 0, __('Table::table.no_active'), 'info', 'fas fa-window-close');
        $listdata->btnAction('delete_custom', -1, __('Table::table.trash'), 'danger', 'fas fa-trash');
        // Build bảng
        $listdata->add('name', __('Coupon::field.name'), 0);
        $listdata->add('code', __('Coupon::field.code'), 1);
        $listdata->add('type', __('Coupon::field.type'), 1);
        $listdata->add('value', __('Coupon::field.value'), 1);
        $listdata->add('max_value', __('Coupon::field.max_value'), 1);
        $listdata->add('select', __('Coupon::field.select'), 1);
        $listdata->add('limit', __('Coupon::field.limit'), 1);
        $listdata->add('used', __('Coupon::field.used'), 1);
        $listdata->add('start_time', __('Coupon::field.start_time'), 0, 'time');
        $listdata->add('end_time', __('Coupon::field.end_time'), 0, 'time');
        $listdata->add('status', 'Trạng thái', 0, 'status');
        $listdata->add('', 'Language', 0, 'lang');
        $listdata->add('', 'Sửa', 0, 'edit');
        $listdata->add('', 'Xóa', 0, 'delete_custom');

        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-9');
        $form->lang($this->table_name);
        $form->text('name', '', 1, __('Coupon::field.name'));
        $form->text('code', '', 1, __('Coupon::field.code'));
        $form->select('type', '', 0, __('Coupon::field.type'), config('SudoCoupon.coupon_type'));
        $form->text('value', '', 1, __('Coupon::field.value'));
        $form->text('max_value', '', 0, __('Coupon::field.max_value'));
        $form->select('select', '', 0, __('Coupon::field.select'), config('SudoCoupon.coupon_select_type'));
        $form->custom('Coupon::selectType');
        $form->text('limit', '', 1, __('Coupon::field.limit'));
        $form->datetimepicker('start_time', '', 1, __('Coupon::field.start_time'));
        $form->datetimepicker('end_time', '', 1, __('Coupon::field.end_time'));
        $form->endCard();
        $form->card('col-lg-3', '');
        $form->action('add');
        $form->radio('status', 1, 'Trạng thái', config('app.status'));
        $form->endCard();
        // Hiển thị form tại view
        $form->hasFullForm();
        return $form->render('create_multi_col');
    }

    public function getDataAjax(Request $request)
    {
        try {
            // type = 1: all, type = 2: categories, type = 3: products
            $type_id = $request->type_id ?? null;
            $data = [];
            switch ($type_id) {
                case 2:
                    $data = \Sudo\Product\Models\Product::where('status', 1)->get();
                    break;
                case 1:
                    $data = \Sudo\Product\Models\ProductCategory::where('status', 1)->get();
                    break;
                default:
                    break;
            }
            return response()->json(json_encode($data));
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests)
    {
        validateForm($requests, 'name', 'Name is required!');
        validateForm($requests, 'code', 'Code is required!');
        $this->validate($requests, [
            'limit' => 'numeric|min:0',
            'code' => 'unique:coupons,code',
            'max_value' => 'numeric|min:0|nullable',
            'value' => 'numeric|min:0|nullable'
        ]);

        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        $max_value = !empty($max_value) ? $max_value : 0;
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'code', 'max_value', 'value', 'type', 'select', 'status', 'limit', 'start_time', 'end_time', 'created_at', 'updated_at');
        $id = $this->models->createRecord($requests, $compact, $this->has_seo);

        if (!empty($type_id)) {
            foreach ($type_id as $type_id) {
                \Sudo\Coupon\Models\CouponSelect::create([
                    'coupon_id' => $id,
                    'type_id' => $type_id
                ]);

            }
//            \Sudo\Product\Models\Product::find($id)->detach($type_id);
        }

        // Điều hướng
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.create_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Dẽ liệu bản ghi hiện tại
        $data_edit = $this->models->where('id', $id)->first();

        $type_ids = \Sudo\Coupon\Models\CouponSelect::select('type_id')->where(['coupon_id' => $id])->pluck('type_id')->toArray();

        $custom_data = ['select_type' => $data_edit->select, 'data' => json_encode($type_ids)];
        $form = new Form;
        $form->card('col-lg-9');
        $form->lang($this->table_name);
        $form->text('name', $data_edit->name, 1, __('Coupon::field.name'));
        $form->text('code', $data_edit->code, 1, __('Coupon::field.code'));
        $form->text('max_value', $data_edit->max_value, 0, __('Coupon::field.max_value'));
        $form->text('value', $data_edit->value, 0, __('Coupon::field.value'));
        $form->select('type', $data_edit->type, 0, __('Coupon::field.type'), config('SudoCoupon.coupon_type'));
        $form->select('select', $data_edit->select, 0, __('Coupon::field.select'), config('SudoCoupon.coupon_select_type'));
        $form->custom('Coupon::selectType', $custom_data);
        $form->text('limit', $data_edit->limit, 1, __('Coupon::field.limit'));
        $form->datetimepicker('start_time', $data_edit->start_time, 1, __('Coupon::field.start_time'));
        $form->datetimepicker('end_time', $data_edit->end_time, 1, __('Coupon::field.end_time'));
        $form->endCard();
        $form->card('col-lg-3', '');
        $form->action('edit');
        $form->radio('status', $data_edit->status, 'Trạng thái', config('app.status'));
        $form->endCard();

        // Hiển thị form tại view
        $form->hasFullForm();
        return $form->render('edit_multi_col', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id)
    {

        // Xử lý validate
        validateForm($requests, 'name', 'Name is required!');
        validateForm($requests, 'code', 'Code is required!');
        $this->validate($requests, [
            'limit' => 'numeric|min:0',
            'code' => 'unique:coupons,code,' . $id,
            'max_value' => 'numeric|min:0|nullable',
            'value' => 'numeric|min:0|nullable'
        ]);
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        $max_value = !empty($max_value) ? $max_value : 0;
        $value = !empty($value) ? $value : 0;
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'code', 'type', 'max_value', 'value', 'select', 'status', 'limit', 'start_time', 'end_time', 'updated_at');
        // Cập nhật tại database
        $this->models->updateRecord($requests, $id, $compact, $this->has_seo);

        \Sudo\Coupon\Models\CouponSelect::where('coupon_id', $id)->delete();

        if (!empty($type_id)) {
            foreach ($type_id as $type_id) {
                \Sudo\Coupon\Models\CouponSelect::create([
                    'coupon_id' => $id,
                    'type_id' => $type_id
                ]);
            }
        }
        // Điều hướng
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    /**
     * Check coupon and product
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function checkCoupon($current_coupon)
    {
        if (!$current_coupon) {
            return [
                'status' => 0,
                'message' => __('Coupon::message.not_exists')
            ];
        }

        if ($current_coupon->used >= $current_coupon->limit || $current_coupon->used < 0) {
            return [
                'status' => 0,
                'message' => __('Coupon::message.has_been_used')
            ];
        }

        if ($current_coupon->end_time < date('Y-m-d H:i:s')) {
            return [
                'status' => 0,
                'message' => __('Coupon::message.time_expired')
            ];
        }

        if (date('Y-m-d H:i:s') < $current_coupon->start_time) {
            return [
                'status' => 0,
                'message' => __('Coupon::message.cant_use')
            ];
        }

        // if not error
        return ['status' => 1, 'message' => ''];
    }

    /**
     * Get price product after use coupon
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getPriceAfterCoupon(Request $request)
    {
        $coupon = $request->coupon ?? null; // code of coupon
        $ids = json_decode($request->ids) ?? null; // id product or category

        try {
            if (empty($ids) || empty($coupon)) {
                return response([
                    'status' => 0,
                    'message' => __('Coupon::message.empty_data')
                ]);
            }

            $current_coupon = $this->models->where(['code' => $coupon, 'status' => 1])->first();

            // Check Coupon
            $result = $this->checkCoupon($current_coupon);

            if (!$result['status']) {
                return response([
                    'status' => $result['status'],
                    'message' => $result['message']
                ]);
            }

            $data = []; // products can use coupon
            $total_sale = 0;
            $total_price = 0;

            $products = \Sudo\Product\Models\Product::whereIn('id', $ids)->get();

            if (count($products) > 0) {
                foreach ($products as $product) {
                    // Check Product or Categories
                    $after_sale_price = $sale_price = 0;
                    switch ($current_coupon->select) {
                        case 2: // check if coupon for product
                            $check = $this->models->where(['code' => $coupon, 'select' => 2])
                                ->whereHas('couponSelect', function ($q) use ($product) {
                                    $q->where('type_id', $product->id);
                                })->get();

                            if (count($check) > 0) {
                                $product_regular_price = $product->price;
                                $after_sale_price = $this->calculator($current_coupon, $product_regular_price);
                                $sale_price = (int)$product_regular_price - (int)$after_sale_price;
                            }

                            break;
                        case 1: // check if coupon for categories
                            $check = $this->models->where(['code' => $coupon, 'select' => 1])
                                ->whereHas('couponSelect', function ($q) use ($product) {
                                    $q->where('type_id', $product->category_id);
                                })->get();

                            if (count($check) > 0) {
                                $product_regular_price = $product->price;
                                $after_sale_price = $this->calculator($current_coupon, $product_regular_price);
                                $sale_price = (int)$product_regular_price - (int)$after_sale_price;
                            }
                            break;
                        default:
                            $product_regular_price = $product->price;
                            $after_sale_price = $this->calculator($current_coupon, $product_regular_price);
                            $sale_price = (int)$product_regular_price - (int)$after_sale_price;
                            break;
                    }

                    $data[] = [
                        'product_id' => $product->id,
                        'is_product_sale' => $after_sale_price ? true : false,
                        'regular_price' => $product_regular_price ?? 0,
                        'product_sale_price' => $sale_price ?? 0,
                        'product_after_sale' => $after_sale_price ?? 0
                    ];

                    $total_sale += $sale_price; // total sale price
                    $total_price += $product->price ?? 0; // total price
                }
            }

            return response(['status' => 1, 'products' => $data, 'total_sale' => $total_sale, 'total_price' => $total_price, 'message' => 'Success!']);

        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Calculator sale price
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function calculator($coupon, $price)
    {
        if ($coupon->type) { // 0: money, 1: percent
            $sale = (int)$coupon->value;
        } else {
            $sale = (int)$price * ((int)$coupon->value / 100);
        }

        if ($sale > $coupon->max_value) { // if price sale greater then max_value, return max_value
            $sale = $coupon->max_value;
        }

        return (int)$price - (int)$sale;
    }
}
