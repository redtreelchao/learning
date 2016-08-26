<?php
$_nav = array(
    'Overview' => array(
        'icon'  => 'fa fa-tachometer',
        'name'  => '概括',
        'url'   => 'demo.php',
        'sub'   => array(),
    ),
    'Reports' => array(
        'icon'  => 'glyphicon glyphicon-list-alt',
        'name'  => '报告',
        'url'   => 'reports.php',
        'sub'   => array(),
        'roles' => array(0,1,2,3,4,8),
    ),
    'Analytics' => array(
        'icon'  => 'glyphicon glyphicon-stats',
        'name'  => '分析',
        'url'   => 'demo.php',
        'sub'   => array(),
        'roles' => array(0,1,2,3,4,8),
    ),

    'hr',

    'District' => array(
        'icon'  => 'glyphicon glyphicon-globe',
        'name'  => '国家城市',
        'url'   => 'district.php',
        'sub'   => array(),
        'roles' => array(0,1,2,3,4,8),
    ),
    'Hotel' => array(
        'icon'  => 'fa fa-building',
        'name'  => '酒店',
        'url'   => 'hotel.php',
        'sub'   => array(
            'list'  => array(
                'name'  => '酒店列表',
                'url'   => 'hotel.php',
            ),
            'room'  => array(
                'name'  => '房型管理',
                'url'   => 'room.php',
            ),
            'package'=> array(
                'name'  => '增值包',
                'url'   => 'package.php',
            ),
            'nation'=> array(
                'name'  => '国籍要求',
                'url'   => 'nation.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),
    'Flight' => array(
        'icon'  => 'fa fa-plane',
        'name'  => '机票',
        'url'   => 'flight.php',
        'sub'   => array(
            'airport'   => array(
                'name'  => '机场信息',
                'url'   => 'airport.php',
            ),
            'list'      => array(
                'name'  => '航班列表',
                'url'   => 'flight.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),
    'Auto' => array(
        'icon'  => 'fa fa-car',
        'name'  => '车辆',
        'url'   => 'car.php',
        'sub'   => array(
            'list'      => array(
                'name'  => '车辆列表',
                'url'   => 'car.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),
    'View' => array(
        'icon'  => 'fa fa-tree',
        'name'  => '景区/体验',
        'url'   => 'view.php',
        'sub'   => array(
            'list'   => array(
                'name'  => '景区/体验列表',
                'url'   => 'view.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),
    'Goods' => array(
        'icon'  => 'fa fa-gift',
        'name'  => '生鲜/商品',
        'url'   => 'goods.php',
        'sub'   => array(
            'list'   => array(
                'name'  => '商品列表',
                'url'   => 'goods.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,7,8),
    ),

    'hr',

    'Supply' => array(
        'icon'  => 'glyphicon glyphicon-briefcase',
        'name'  => '供应商',
        'url'   => 'supply.php',
        'sub'   => array(
            'area'  => array(
                'name'  => '区域管理',
                'url'   => 'supply.php?method=area',
            ),
            'list'=> array(
                'name'  => '供应商列表',
                'url'   => 'supply.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),

    'Product' => array(
        'icon'  => 'glyphicon glyphicon-shopping-cart',
        'name'  => '产品',
        'url'   => 'product.php',
        'sub'   => array(
            'list'  => array(
                'name'  => '产品列表',
                'url'   => 'product.php',
            ),
            'profit'=> array(
                'name'  => '产品利润',
                'url'   => 'profit.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),

    'Tour' => array(
        'icon'  => 'fa fa-location-arrow',
        'name'  => '定制游',
        'url'   => 'tourcard.php',
        'sub'   => array(
            'area'   => array(
                'name'  => '产品线',
                'url'   => 'tour.php?method=area',
            ),
            'designer'   => array(
                'name'  => '行程设计师',
                'url'   => 'tour.php?method=designer',
            ),
            'card'   => array(
                'name'  => '定制需求卡',
                'url'   => 'tourcard.php',
            ),
            'order'   => array(
                'name'  => '行程规划',
                'url'   => 'tourorder.php',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),

    'hr',

    'Order' => array(
        'icon'  => 'glyphicon glyphicon-list-alt',
        'name'  => '订单',
        'url'   => 'order.php',
        'sub'   => array(
            'list'  => array(
                'name'  => '订单列表',
                'url'   => 'order.php',
            ),
            'export'=> array(
                'name'  => '订单导出',
                'url'   => 'order.php?method=export',
                'roles' => array(0,1,2,3,8),
            ),
            'product'=> array(
                'name'  => '产品展示',
                'url'   => 'order.php?method=product',
                'roles' => array(0,1,2,3,8),
            ),
        ),
        'roles' => array(0,1,2,3,4,7,8),
    ),

    'Sms' => array(
        'icon'  => 'glyphicon glyphicon-envelope',
        'name'  => '短信',
        'url'   => 'sms.php',
        'sub'   => array(
            'list'  => array(
                'name'  => '短信发送列表',
                'url'   => 'sms.php',
            ),
            'profit'=> array(
                'name'  => '发送短信',
                'url'   => 'sms.php?method=send',
            ),
        ),
        'roles' => array(0,1,2,3,4,8),
    ),

    'hr',

    'User' => array(
        'icon'  => 'glyphicon glyphicon-user',
        'name'  => '用户',
        'url'   => 'user.php',
        'sub'   => array(
            'list'  => array(
                'name'  => '用户列表',
                'url'   => 'user.php',
            ),
            'profit'=> array(
                'name'  => '角色权限',
                'url'   => 'user.php?method=role',
            ),
        ),
        'roles' => array(0),
    ),


);

?><div class=" col-xs-12 col-sm-1 col-md-2 sidebar">
    <div class="row">

        <ul class="nav nav-sidebar">
            <?php foreach ($_nav as $_key => $_n) { ?>
            <?php
            if ($_n == 'hr') {
            ?>
        </ul>

        <ul class="nav nav-sidebar">
            <?php
                continue;
            }
            ?>
            <li class="<?php echo $_key; if($nav == $_key) echo ' active'; if(!empty($_n['roles']) && !in_array($_SESSION['role'], $_n['roles'])) echo ' hidden'; ?>">
                <a href="<?php echo BASE_URL, $_n['url']; ?>"><span class="<?php echo $_n['icon']; ?>"></span><?php echo $_n['name']; ?></a>
                <?php if($_n['sub']) { ?>
                <ul class="nav sub-sidebar">
                    <?php foreach ($_n['sub'] as $_k => $_s) { ?>
                    <li class="<?php if($nav == $_key && $subnav == $_k) echo 'active'; if(!empty($_s['roles']) && !in_array($_SESSION['role'], $_s['roles'])) echo ' hidden'; ?>"><a href="<?php echo BASE_URL, $_s['url']; ?>"><?php echo $_s['name']; ?></a></li>
                    <?php } ?>
                </ul>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>

    </div>
</div>