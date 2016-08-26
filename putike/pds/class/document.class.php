<?php
// 接口文档补充
class document
{
    // all method and document
    static public $func = array(
        'district'  => array(
            'country'   => array(
                'name'  => '国家',
                'args'  => array(),
            ),
            'city'      => array(
                'name'  => '城市',
                'args'  => array('country'=>'国家id'),
            ),
            'district'  => array(
                'name'  => '城市',
                'args'  => array('city'=>'城市id', 'type'=>'类型，district 行政区'),
            ),
        ),
        'hotel' => array(
            'login'     => array(
                'name'  =>  '酒店端用户登录',
                'args'  =>  array('username'=>'用户名，必填', 'password'=>'密码，必填'),
            ),
            'link'      => array(
                'name'    => '通过艺龙获取酒店基本信息',
                'args'    => array('url'=>null),
            ),
            'types'     => array(
                'name'    => '酒店类型',
                'args'    => array(),
            ),
            'load'      => array(
                'name'    => '读取酒店信息',
                'args'    => array('id'=>null),
            ),
            'create'    => array(
                'name'    => '创建新酒店',
                'args'    => array('name'=>'酒店名称，必填','pms'=>'PMS,选填', 'en'=>'酒店英文名，选填', 'country'=>'国家，必填，ID', 'city'=>'城市，必填，ID', 'district'=>'行政区，选填，ID', 'address'=>'地址，必填', 'tel'=>'电话，必填', 'lng'=>'经度，必填', 'lat'=>'纬度，必填', 'type'=>'酒店类型，选填', 'star'=>'星级，选填', 'roomnum'=>'房间数，必填', 'opening'=>'开业时间，必填', 'redecorate'=>'最近装修时间，必填', 'bland'=>'品牌，选填', 'checkin'=>'入住时间，必填', 'checkout'=>'离店时间，必填', 'policies'=>'其他政策，选填，数组', 'intro'=>'特色介绍，必填', 'edges'=>'酒店亮点，选填，数组', 'tags'=>'标签库，选填，数组', 'status'=>'状态，必填 1有效 0无效'),
            ),
            'update'    => array(
                'name'    => '更新酒店',
                'args'    => array('id'=>'酒店ID，必填', 'name'=>'酒店名称，必填','pms'=>'PMS,选填', 'en'=>'酒店英文名，选填', 'country'=>'国家，必填，ID', 'city'=>'城市，必填，ID', 'address'=>'地址，必填', 'tel'=>'电话，必填', 'lng'=>'经度，必填', 'lat'=>'纬度，必填', 'type'=>'酒店类型，选填', 'star'=>'星级，选填', 'roomnum'=>'房间数，必填', 'opening'=>'开业时间，必填', 'redecorate'=>'最近装修时间，必填', 'bland'=>'品牌，选填', 'checkin'=>'入住时间，必填', 'checkout'=>'离店时间，必填', 'policies'=>'其他政策，选填，数组', 'intro'=>'特色介绍，必填', 'edges'=>'酒店亮点，选填，数组', 'tags'=>'标签库，选填，数组', 'status'=>'状态，必填 1有效 0无效'),
            ),
            'search'    => array(
                'name'    => '酒店检索',
                'args'    => array('country'=>'国家名', 'province'=>'省份名', 'city'=>'城市名', 'id'=>'酒店ID', 'name'=>'中文名', 'en'=>'英文名', 'brand'=>'品牌', 'type'=>'类型', 'star'=>'星级', 'limit'=>'分页数，默认15', 'export'=>'是否为导出'),
            ),
            'all'       => array(
                'name'    => '所有酒店',
                'args'    => array('country'=>'国家', 'city'=>'城市'),
            ),
            'prepaymin' => array(
                'name'    => '预付最小值',
                'args'    => array('checkin'=>'入住时间，必填', 'checkout'=>'离店时间，必填', 'hotels'=>'酒店IDS，逗号隔开'),
            ),
            'history' => array(
                'name'    => '操作信息',
                'args'    => array('id'=>'酒店ID，必填'),
            ),
            'around_bind'=>array(
                'name'  => '绑定周边',
                'args'    => array('hotel_id'=>'酒店ID，必填', 'around_ids'=>'周边ID，必填（如需批量操作，则传数组）'),
            ),
            'around_unbind'=>array(
                'name'  => '取消绑定周边',
                'args'    => array('hotel_id'=>'酒店ID，必填', 'around_id'=>'周边ID，必填'),
            ),
            'around_list'=>array(
                'name'  => '与当前酒店绑定的周边列表',
                'args'    => array('hotel_id'=>'酒店ID，必填', 'limit'=>'分页数，选填，默认10'),
            ),
        ),
        'room'      => array(
            'load'      => array(
                'name'    => '读取酒店房型信息',
                'args'    => array('hotel'=>'酒店ID，必填'),
            ),
            'save'      => array(
                'name'    => '保存房型信息',
                'args'    => array('hotel'=>'酒店ID，必填', 'roomsummary'=>'酒店客房概括，必填', 'tags'=>'设施等标签库，数组', 'rooms'=>'客房详细内容，数组'),
                'array'   => array(
                    'rooms'  => array(1,
                        'id'    => 'ROOMID，选填，有则更新/没有则创建',
                        'name'  => '房型名，必填',
                        'en'    => '房型英文名',
                        'pics'  => '房型图片，数组',
                        'area'  => '房间大小',
                        'floor' => '楼层',
                        'adult' => '可入住成人数',
                        'child' => '可入住儿童数',
                        'bed'   => '床型',
                        'smoke' => '是否可无烟处理 0/1',
                        'pet'   => '是否可带宠物 0/1',
                        'addbe' => '是否可加床 0/1',
                        'scenery'   => '房型景观',
                        'intro' => '房型特点介绍',
                    ),
                ),
            ),
        ),
        'around'=>array(
            'create'    => array(
                'name'  => '创建周边',
                'args'    => array('name'=>'酒店名称，必填', 'en'=>'酒店英文名，必填', 'country'=>'国家，必填，ID', 'city'=>'城市，必填，ID', 'lng'=>'经度，必填', 'lat'=>'纬度，必填', 'type'=>'周边类型，必填', 'status'=>'状态，1有效 0无效 默认1'),
            ),
            'update'    => array(
                'name'  => '修改周边',
                'args'    => array('id'=>'周边id, 必填', 'name'=>'酒店名称，必填', 'en'=>'酒店英文名，必填', 'country'=>'国家，必填，ID', 'city'=>'城市，必填，ID', 'lng'=>'经度，必填', 'lat'=>'纬度，必填', 'type'=>'周边类型，必填', 'status'=>'状态，必填 1有效 0无效'),
            ),
            'delete'    => array(
                'name'  =>'删除周边',
                'args'  => array('id'=>'周边id，必填'),
            ),
            'search'    =>array(
                'name'  =>  '查询周边',
                'args'  => array('keywords'=>'关键字', 'hotel_id'=>'酒店id','distance'=>'酒店与周边距离', 'limit'=>'分页数，选填，默认10')
            ),
            'type'  => array(
                'name'  => '周边类型',
                'args'  =>array(),
            ),
        ),
        'amenserv'  => array(
            'amenity'           => array(
                'name'    => '读取酒店设施',
                'args'    => array('hotel'=>'酒店ID，必填'),
            ),
            'update_amenity'    => array(
                'name'    => '更新酒店设施',
                'args'    => array('hotel'=>'酒店ID，必填', 'amenities'=>'设施内容，数组'),
                'array'   => array(
                    'amenities'  => array(1,'pic'=>'图片', 'text'=>'设施描述'),
                ),
            ),
            'activity'          => array(
                'name'    => '读取酒店活动',
                'args'    => array('hotel'=>'酒店ID，必填'),
            ),
            'update_activity'   => array(
                'name'    => '更新酒店活动',
                'args'    => array('hotel'=>'酒店ID，必填', 'activities'=>'活动，数组'),
                'array'   => array(
                    'activities' => array(1,'pic'=>'图片', 'text'=>'活动描述'),
                ),
            ),
            'service'           => array(
                'name'    => '读取酒店服务',
                'args'    => array('hotel'=>'酒店ID，必填'),
            ),
            'update_service'    => array(
                'name'    => '更新酒店服务',
                'args'    => array('hotel'=>'酒店ID，必填', 'services'=>'服务内容，数组', 'tags'=>'标签库，数组'),
                'array'   => array(
                    'services'  => array(1,'pic'=>'图片', 'text'=>'服务描述'),
                ),
            ),
        ),
        'tag'       => array(
            'types'     => array(
                'name'    => '标签类型',
                'args'    => array('types'=>'选择类型，amenity/酒店设施,facility/房间设施,service/服务,view/周边,design/设计风格,crowd/人群,atmosphere/氛围,characteristic/其他特点,catering/食品饮料（室内）,appliances/多媒体科技,bathroom/浴室,washing/备品品牌,othserve/其他服务', 'tags'=>'是否读取所含标签，默认0不读取'),
            ),
            'create'    => array(
                'name'    => '新建标签',
                'args'    => array('name'=>'标签内容', 'type'=>'标签类型'),
            ),
        ),
        'product'   => array(
            'status'    => array(
                'name'    => '产品全部状态',
                'args'    => array('type'=>'*类型 1酒店 4机+酒', 'payment'=>'*支付类型，预付prepay,券类ticket', 'status'=>'状态 0,-1下架 1正常'),
            ),
            'search'    => array(
                'name'    => '产品检索',
                'args'    => array('keyword'=>'关键词/产品id', 'type'=>'*类型 1酒店 4机+酒', 'payment'=>'*支付类型，预付prepay,券类ticket', 'source'=>'出发地', 'target'=>'目的地', 'checkin'=>'入住时间', 'checkout'=>'离店时间', 'min_price'=>'最低价', 'max_price'=>'最高价', 'limit'=>'分页数'),
            ),
            'calendar'    => array(
                'name'    => '券类预订每日库存',
                'args'    => array('item'=>'券类子项id'),
            ),
        ),
        'order' => array(
            'booking'   => array(
                'name'    => 'XML复合数据下单',
                'args'    => array(),
            ),
            'create'    => array(
                'name'    => '创建订单',
                'args'    => array('currency'=>'货币，必填，1 RMB', 'paytype'=>'支付类型，必填，预付prepay,券类ticket', 'contact'=>'联系人，必填', 'tel'=>'联系电话，必填', 'email'=>'联系邮箱，选填', 'ip'=>'访问IP，选填'),
            ),
            'room'      => array(
                'name'    => '增加入住房间',
                'args'  => array('order'=>'*订单号', 'code'=>'*产品代码', 'num'=>'*数量', 'product'=>'产品名', 'peoples'=>'?入住人（数组，入住人|所需床型）', 'checkin'=>'?入住时间', 'checkout'=>'?离店时间', 'remark'=>'备注'),
            ),
            'flight'    => array(
                'name'    => '增加机票',
                'args'    => array('order'=>'*订单号', 'code'=>'*产品代码', 'num'=>'*数量', 'product'=>'产品名', 'date'=>'*起飞日期', 'peoples'=>'乘机人（数组，姓名|类型|凭据|生日）', 'remark'=>'备注'),
            ),
            'auto'      => array(
                'name'      => '增加车辆',
                'args'      => array('order'=>'*订单号', 'code'=>'*产品代码', 'num'=>'*数量', 'product'=>'产品名', 'date'=>'*出发日期', 'peoples'=>'?驾驶人（数组，驾驶人|证件号码）', 'remark'=>'备注'),
            ),
            'goods'      => array(
                'name'      => '增加商品',
                'args'      => array('order'=>'*订单号', 'code'=>'*产品代码', 'num'=>'*数量', 'product'=>'产品名', 'contact'=>'*快递联系人', 'tel'=>'*联系电话', 'address'=>'*快递地址', 'remark'=>'备注'),
            ),
            'apply'      => array(
                'name'      => '预订使用（酒店券）',
                'args'      => array(   'order'=>'*订单号', 'ticket'=>'*券id', 'group'=>'*批次编号（使用第1张传0，将返回group值，同一次使用的其他券沿用此值）',
                                        'checkin'=>'*入住时间', 'adult'=>'*成人数', 'child'=>'*儿童数', 'people'=>'*入住人姓名', 'birth'=>'?儿童出生日期Y-m-d',
                                        'bed'=>'床型 T双 D大', 'tel'=>'*联系电话', 'email'=>'*联系邮箱', 'require'=>'备注'),
            ),
            'invoice'   => array(
                'name'    => '申请发票',
                'args'    => array('order'=>'*订单号', 'payer'=>'*抬头', 'item'=>'*开票项', 'receiver'=>'*快递联系人', 'receivertel'=>'*联系电话', 'receiveraddr'=>'*快递地址'),
            ),
            'pay'       => array(
                'name'    => '确认订单支付',
                'args'    => array('order'=>'*订单号', 'time'=>'*支付时间', 'type'=>'*支付方式', 'account'=>'*对方账号', 'trade'=>'*交易流水号', 'rebate'=>'优惠金额，默认0', 'rebatetype'=>'优惠方式/内容'),
            ),
            'refund'    => array(
                'name'    => '申请退款',
                'args'    => array('order'=>'*订单号', 'remark'=>'客户备注'),
            ),
            'view'      => array(
                'name'    => '读取订单',
                'args'    => array('order'=>'*订单号'),
            ),
        ),
        'user'  => array(
            'login'     => array(
                'name'  =>  '用户登录',
                'args'  =>  array('username'=>'手机/邮箱/用户名，必填', 'password'=>'密码，必填'),
            ),
            'info'      => array(
                'name'  =>  '用户信息',
                'args'  =>  array('uid'=>'用户ID，选填，留空为本人信息'),
            ),
        ),
        'picture'  => array(
            'gallery_update'  => array(
                    'name'    => '创建/修改图片集',
                    'args'    => array('id'=>'图片集ID，选填', 'name'=>'名称，必填', 'city'=>'城市，修改选填', 'hotel'=>'酒店，修改选填'),
                    ),
            'gallery'         => array(
                    'name'    => '读取图片集',
                    'args'    => array('id'=>'必填', 'type'=>'图片大类，选填', 'order'=>'排序，选填 update/type', 'limit'=>'分页数，选填，默认10'),
                    ),
            'gallery_hotel'   => array(
                    'name'    => '读取酒店图片集',
                    'args'    => array('hotel'=>'酒店ID，必填',  'order'=>'排序，选填 update/type', 'limit'=>'分页数，选填，默认10'),
                    ),
            'recently'        => array(
                    'name'    => '最近修改的图片集',
                    'args'    => array('limit'=>'图片集条数，选填，默认3', 'picture'=>'图片集展示图片数，选填，默认4'),
                    ),
            'search'          => array(
                    'name'    => '检索图片',
                    'args'    => array('keyword'=>'关键词，必填', 'type'=>'搜索类型，必填，gallery/picture', 'limit'=>'分页数，选填，默认10，可为0'),
                    ),
            'queue'           => array(
                    'name'    => '待上传任务',
                    'args'    => array('limit'=>'分页数，选填，默认10'),
                    ),
            'unbind'          => array(
                    'name'    => '未关联图片集',
                    'args'    => array(),
                    ),
            'bind'            => array(
                    'name'    => '图片集关联酒店',
                    'args'    => array('id'=>'图片集ID，必填', 'hotel'=>'酒店ID，必填'),
                    ),
            'type_update'     => array(
                    'name'    => '创建/修改分类',
                    'args'    => array('id'=>'必填', 'name'=>'名称，必填', 'pid'=>'大类id，必填，pid=2不能添加子项'),
                    ),
            'type'            => array(
                    'name'    => '读取分类列表',
                    'args'    => array('gallery'=>'图片集ID，选填'),
                    ),
            'type_delete'     => array(
                    'name'    => '删除分类',
                    'args'    => array('id'=>'必填'),
                    ),
            'tags'            => array(
                    'name'    => '读取可用标签',
                    'args'    => array('keyword'=>'关键词，选填', 'num'=>'显示数量，选填，默认10'),
                    ),
            'upload'          => array(
                    'name'    => '上传图片',
                    'args'    => array('file'=>'图片内容base64，带文件头', 'name'=>'名称，选填', 'lng'=>'维度，选填', 'lat'=>'经度，选填','gallery'=>'图片集，选填'),
                    ),
            'load'            => array(
                    'name'    => '读取图片信息',
                    'args'    => array('id'=>'图片ID，必填'),
                    ),
            'edit'            => array(
                    'name'    => '修改图片',
                    'args'    => array('id'=>'图片ID，必填', 'title'=>'标题，必填', 'intro'=>'介绍，选填', 'gallery'=>'图片集，必填', 'type'=>'大类，必填', 'subtype'=>'子类，必填', 'copyright'=>'版权，选填', 'tags'=>'标签，选填，数组'),
                    ),
            'edit_multi'      => array(
                    'name'    => '修改多个图片',
                    'args'    => array('ids'=>'图片ID，必填，数组', 'titles'=>'标题，必填，数组', 'gallery'=>'图片集，必填', 'type'=>'大类，必填', 'subtype'=>'子类，必填'),
                    ),
            'delete'          => array(
                    'name'    => '删除图片',
                    'args'    => array('ids'=>'图片ID，必填，数组'),
                    ),
        ),
    );


}