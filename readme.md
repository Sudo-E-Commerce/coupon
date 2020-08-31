## Hướng dẫn sử dụng Sudo Coupon ##

**Giới thiệu:** Đây là package dùng để quản lý Coupon Ecommerce.

Mặc định package sẽ tạo ra giao diện quản lý cho toàn bộ coupons

### Cài đặt để sử dụng ###

- Package cần phải có base `sudo/core`, `sudo/products` để có thể hoạt động không gây ra lỗi

### Cách sử dụng ###

#### Giao diện trang quản trị ####

##### Thêm đoạn code sau vào config/SudoMenu.php để hiển thị menu ####
    [
        'type' 				=> 'multiple',
        'name' 				=> 'Coupon',
        'icon' 				=> 'fas fa-barcode',
        'childs' => [
            [
                'name' 		=> 'Thêm mới',
                'route' 	=> 'admin.coupons.create',
                'role' 		=> 'coupons_create'
            ],
            [
                'name' 		=> 'Danh sách',
                'route' 	=> 'admin.coupons.index',
                'role' 		=> 'coupons_index',
                'active' 	=> [ 'admin.coupons.create', 'admin.coupons.show', 'admin.coupons.edit' ]
            ]
        ]
    ]
##### Thêm đoạn code sau vào config/Module.php để tiến hành phân quyền ####
    'coupons' => [
        'name' 			=> 'Coupon',
        'permision' 	=> [
            [ 'type' => 'index', 'name' => 'Truy cập' ],
            [ 'type' => 'create', 'name' => 'Thêm' ],
            [ 'type' => 'edit', 'name' => 'Sửa' ],
            [ 'type' => 'restore', 'name' => 'Lấy lại' ],
            [ 'type' => 'delete', 'name' => 'Xóa' ],
        ],
    ],
#### Giao diện người dùng ####
##### Cách sử dụng #####
-  @include('Coupon::form', ['ids' => [1,2,3], 'placeholder' => 'Enter your code']). 
-  Trong đó:
-   ids: mảng các id products.
-   placeholder: hiển thị text ở input.

##### Call API coupons/price-after-coupon?ids=[1,2,3]&coupon=tmt để áp dụng coupon cho sản phẩm. #####
-   Example: `https://chanhtuoi.com/coupons/price-after-coupon?ids=[1,2,3]&coupon=tmt`

##### Kết quả trả về: #####
            
##### Có lỗi xảy ra #####

    [
        "status" => 0,
        "message" => "Mã giảm giá không tồn tại"
    ]
##### Nếu thành công #####

    [
        message: "Success!"
        products: Array(3)
            0: {product_id: 1, is_product_sale: true, regular_price: 125, product_sale_price: 20, product_after_sale: 105}
            1: {product_id: 2, is_product_sale: true, regular_price: 175, product_sale_price: 20, product_after_sale: 155}
            2: {product_id: 3, is_product_sale: false, regular_price: 175, product_sale_price: 0, product_after_sale: 0}
        status: 1
        total_price: 550
        total_sale: 40
    ]
    
Trong đó:
- Status: Tình trạng, 0: fail, 1: success.
- Products: Mảng các products đã truyền vào
- Product_id: Id của product
- Is_product_sale: Sản phẩm có được sử dụng coupon hay không.
- Regular_price: Giá gốc sản phẩm.
- Product_sale_price: Số lượng tiền được giảm khi sử dụng coupon.
- Product_after_sale: Giá sản phẩm sau khi sale.
- Total_price: Tổng số tiền của sản phẩm khi chưa sử dụng coupon.
- Total_sale: Tổng số tiền được giảm khi sử dụng coupon. 
    
##### Call API coupons/get-coupons/{product_id} để lấy toàn bộ coupons của sản phẩm. #####
-   Example: `https://chanhtuoi.com/coupons/get-coupons/1`
    
#### Kết quả trả về ####
##### Có lỗi xảy ra #####
    {
        "status": 0,
        "message": "Dữ liệu trống!"
    }
##### Nếu thành công #####
    {
        "status": 1,
        "data": [
            {
                "id": 3,
                "name": "trongtm",
                "code": "trongtm",
                "type": 0,
                "max_value": 100,
                "value": 50,
                "select": 2,
                "limit": 5,
                "used": 5,
                "start_time": "2020-08-19 00:00:00",
                "end_time": "2020-09-04 00:00:00",
                "status": 1,
                "created_at": "2020-08-29T01:58:48.000000Z",
                "updated_at": "2020-08-29T02:21:13.000000Z"
            }
        }
    }
#### Update coupon sau khi sử dụng ###
Sau khi sử dụng coupon và đặt hàng thành công, update lại số lượng coupon bằng cách gọi method updateQuantity() in model Coupon

    $coupon = \Sudo\Coupon\Models\Coupon::where('code', 'coupon-code')->first();
    $coupon = $coupon->updateQuantity();
    
Nó sẽ trả về mảng nếu thành công.

    ['status' => true, 'message' => 'Success!'] 