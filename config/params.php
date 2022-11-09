<?php

return [
    'adminEmail' => $local_config['adminEmail'],
    'adminEmail_password' => $local_config['adminEmail_password'],

    'gii.allowedIPs' => $local_config['gii.allowedIPs'],

    # 工单级别
    'order_level' => [
        1   => 'P0',
        2   => 'P1',
        3   => 'P2',
        4   => 'P3',
    ],

    # 问题归类
    'order_classify' => [
        1   =>  '用户操作问题',
        2   =>  '系统Bug',
        3   =>  '新需求',
        4   =>  '导入导出/帮助类',
        5   =>  '遗留/需排期',
    ],

    // 用户配置信息
    # ！！！ 用户部门决定操作权限，关联系统决定可见范围。 ！！！
    # 部门（起角色作用）
    'user_department' => [
        0   =>  '管理组',
        1   =>  '业务组',
    ],

    # 用户状态
    'user_status' => [
        0   =>  '不可用',
        1   =>  '可用',
    ],

    # 工单状态
    'order_status' => [
        0   => '待处理',
        10  => '处理中',
        20  => '已完成',
    ],

    //UEditor 通用配置（个别需求可在单独配置）
    'UEditor_clientOptions' => [

        # 文件上传位置等信息 在用到UEditor的Controller 中配置

        //最大输入字数
        'maximumWords' => '100000',
        //已输入/可输入字数提示
        'wordCountMsg' => '',
        //编辑区域大小
        'initialFrameHeight' => '200',
        //图片上传大小限制 单位B
        'imageMaxSize' => 2048000,
        //设置语言
        'lang' =>'zh-cn', //英文为 en
        //定制菜单
        'toolbars' => [
            [
                'fullscreen', 'source', 'undo', 'redo', '|',
                'fontsize',
                'bold', 'italic', 'underline',
                'removeformat', 'formatmatch',
                 '|', 'insertcode', '|', 'attachment', 'insertimage'
            ],
        ],
        //鼠标右键菜单功能
        'enableContextMenu' => false,
    ]

    /*
     * 富文本菜单按钮参考
     * 'toolbars' => [
        [
            'anchor', //锚点
            'undo', //撤销
            'redo', //重做
            'bold', //加粗
            'indent', //首行缩进
            'snapscreen', //截图
            'italic', //斜体
            'underline', //下划线
            'strikethrough', //删除线
            'subscript', //下标
            'fontborder', //字符边框
            'superscript', //上标
            'formatmatch', //格式刷
            'source', //源代码
            'blockquote', //引用
            'pasteplain', //纯文本粘贴模式
            'selectall', //全选
            'print', //打印
            'preview', //预览
            'horizontal', //分隔线
            'removeformat', //清除格式
            'time', //时间
            'date', //日期
            'unlink', //取消链接
            'insertrow', //前插入行
            'insertcol', //前插入列
            'mergeright', //右合并单元格
            'mergedown', //下合并单元格
            'deleterow', //删除行
            'deletecol', //删除列
            'splittorows', //拆分成行
            'splittocols', //拆分成列
            'splittocells', //完全拆分单元格
            'deletecaption', //删除表格标题
            'inserttitle', //插入标题
            'mergecells', //合并多个单元格
            'deletetable', //删除表格
            'cleardoc', //清空文档
            'insertparagraphbeforetable', //"表格前插入行"
            'insertcode', //代码语言
            'fontfamily', //字体
            'fontsize', //字号
            'paragraph', //段落格式
            'simpleupload', //单图上传
            'insertimage', //多图上传
            'edittable', //表格属性
            'edittd', //单元格属性
            'link', //超链接
            'emotion', //表情
            'spechars', //特殊字符
            'searchreplace', //查询替换
            'map', //Baidu地图
            'gmap', //Google地图
            'insertvideo', //视频
            'help', //帮助
            'justifyleft', //居左对齐
            'justifyright', //居右对齐
            'justifycenter', //居中对齐
            'justifyjustify', //两端对齐
            'forecolor', //字体颜色
            'backcolor', //背景色
            'insertorderedlist', //有序列表
            'insertunorderedlist', //无序列表
            'fullscreen', //全屏
            'directionalityltr', //从左向右输入
            'directionalityrtl', //从右向左输入
            'rowspacingtop', //段前距
            'rowspacingbottom', //段后距
            'pagebreak', //分页
            'insertframe', //插入Iframe
            'imagenone', //默认
            'imageleft', //左浮动
            'imageright', //右浮动
            'attachment', //附件
            'imagecenter', //居中
            'wordimage', //图片转存
            'lineheight', //行间距
            'edittip ', //编辑提示
            'customstyle', //自定义标题
            'autotypeset', //自动排版
            'webapp', //百度应用
            'touppercase', //字母大写
            'tolowercase', //字母小写
            'background', //背景
            'template', //模板
            'scrawl', //涂鸦
            'music', //音乐
            'inserttable', //插入表格
            'drafts', // 从草稿箱加载
            'charts', // 图表
        ]
    ]*/


];
