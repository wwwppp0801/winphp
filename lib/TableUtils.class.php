<?php
class TableUtils{
    public static $currencyTable=[
        'CNY'=>'￥',
        'USD'=>'＄',
    ];

    public static function getUnitShow($unit){
        return self::$currencyTable[$unit];
    }

    public static function getAuth($authKey){
        return self::$authTable[$authKey];
    }

    public static $authTable=[
        # IndexController
        'index_index'=>'',
        'index_select_search'=>'',
        'index_update'=>'',
        'index_search'=>'',
        'index_delete'=>'',
        'index_read'=>'',
        'index_select'=>'',
        'index_create'=>'',

        # LogisticController
        'logistic_index'=>'',
        'logistic_select_search'=>'',
        'logistic_update'=>'',
        'logistic_search'=>'',
        'logistic_delete'=>'',
        'logistic_read'=>'',
        'logistic_select'=>'',
        'logistic_create'=>'',

        # StockAmountController
        'stockamount_index'=>'',
        'stockamount_select_search'=>'',
        'stockamount_update'=>'',
        'stockamount_search'=>'',
        'stockamount_delete'=>'',
        'stockamount_read'=>'',
        'stockamount_select'=>'',
        'stockamount_create'=>'',

        # UserController
        'user_index'=>'',
        'user_select_search'=>'',
        'user_update'=>'',
        'user_search'=>'',
        'user_delete'=>'',
        'user_read'=>'',
        'user_select'=>'',
        'user_create'=>'',

        # SystemLogController
        'systemlog_index'=>'',
        'systemlog_select_search'=>'',
        'systemlog_update'=>'',
        'systemlog_search'=>'',
        'systemlog_delete'=>'',
        'systemlog_read'=>'',
        'systemlog_select'=>'',
        'systemlog_create'=>'',

        # BuyerController
        'buyer_index'=>'',
        'buyer_select_search'=>'',
        'buyer_update'=>'',
        'buyer_search'=>'',
        'buyer_delete'=>'',
        'buyer_read'=>'',
        'buyer_select'=>'',
        'buyer_create'=>'',

        # LiveController
        'live_index'=>'',
        'live_select_search'=>'',
        'live_update'=>'',
        'live_search'=>'',
        'live_delete'=>'',
        'live_read'=>'',
        'live_select'=>'',
        'live_create'=>'',

        # PaymentController
        'payment_index'=>'',
        'payment_select_search'=>'',
        'payment_update'=>'',
        'payment_search'=>'',
        'payment_delete'=>'',
        'payment_read'=>'',
        'payment_select'=>'',
        'payment_create'=>'',

        # PackController
        'pack_index'=>'',
        'pack_select_search'=>'',
        'pack_update'=>'',
        'pack_search'=>'',
        'pack_delete'=>'',
        'pack_read'=>'',
        'pack_select'=>'',
        'pack_create'=>'',

        # StockController
        'stock_index'=>'',
        'stock_select_search'=>'',
        'stock_update'=>'',
        'stock_search'=>'',
        'stock_delete'=>'',
        'stock_read'=>'',
        'stock_select'=>'',
        'stock_create'=>'',

        # OrderController
        'order_index'=>'',
        'order_select_search'=>'',
        'order_update'=>'',
        'order_search'=>'',
        'order_delete'=>'',
        'order_read'=>'',
        'order_select'=>'',
        'order_create'=>'',

        # AdminController
        'admin_index'=>'auth',
        'admin_select_search'=>'auth',
        'admin_update'=>'auth',
        'admin_search'=>'auth',
        'admin_delete'=>'auth',
        'admin_read'=>'auth',
        'admin_select'=>'auth',
        'admin_create'=>'auth',

        # PermissionController
        'permission_index'=>'auth',
        'permission_select_search'=>'auth',
        'permission_update'=>'auth',
        'permission_search'=>'auth',
        'permission_delete'=>'auth',
        'permission_read'=>'auth',
        'permission_select'=>'auth',
        'permission_create'=>'auth',

        # GroupController
        'group_index'=>'auth',
        'group_select_search'=>'auth',
        'group_update'=>'auth',
        'group_search'=>'auth',
        'group_delete'=>'auth',
        'group_read'=>'auth',
        'group_select'=>'auth',
        'group_create'=>'auth',

        # RolePermissionController
        'rolepermission_index'=>'auth',
        'rolepermission_select_search'=>'auth',
        'rolepermission_update'=>'auth',
        'rolepermission_search'=>'auth',
        'rolepermission_delete'=>'auth',
        'rolepermission_read'=>'auth',
        'rolepermission_select'=>'auth',
        'rolepermission_create'=>'auth',

        # AdminController
        'admingroup_index'=>'auth',
        'admingroup_select_search'=>'auth',
        'admingroup_update'=>'auth',
        'admingroup_search'=>'auth',
        'admingroup_delete'=>'auth',
        'admingroup_read'=>'auth',
        'admingroup_select'=>'auth',
        'admingroup_create'=>'auth',
    ];
}

