<?php
use yii\helpers\Url;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?php echo Yii::$app->user->identity->user_name; ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <!--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>-->
        <!-- /.search form -->
        <?php if (Yii::$app->user->identity->isSuperAdmin) : ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => '导航菜单', 'options' => ['class' => 'header']],
                    ['label' => '首页', 'icon' => 'menu-icon fa fa-home', 'url' => ['/site/index']],
                    ['label' => '用户管理', 'icon' => 'menu-icon fa fa-user', 'url' => ['/user/index']],
                    ['label' => '工单管理', 'icon' => 'dashboard', 'url' => ['/order/index']],
                    //['label' => '投标管理', 'icon' => 'dashboard', 'url' => ['/bid/index']],
                    ['label' => '测试模块', 'url' => ['/test/index']],
                    [
                        'label' => '系统管理',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                            [
                                'label' => '用户权限管理',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => '分配', 'icon' => 'dashboard', 'url' =>['/admin/assignment'],],
                                    ['label' => '角色列表', 'icon' => 'dashboard', 'url' =>['/admin/role'],],
                                    ['label' => '权限列表', 'icon' => 'dashboard', 'url' =>['/admin/permission'],],
                                    ['label' => '路由列表', 'icon' => 'dashboard', 'url' =>['/admin/route'],],
//                                    ['label' => '规则列表', 'icon' => 'dashboard', 'url' =>['/admin/rule'],],
                                    ['label' => '菜单列表', 'icon' => 'dashboard', 'url' =>['/admin/menu'],],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ) ?>
        <?php else: ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => '导航菜单', 'options' => ['class' => 'header']],
                    ['label' => '首页', 'icon' => 'menu-icon fa fa-home', 'url' => ['/site/index']],
                    ['label' => '工单管理', 'icon' => 'dashboard', 'url' => ['/order/index']],
                    //['label' => '投标管理', 'icon' => 'dashboard', 'url' => ['/bid/index']],
                    ['label' => '测试模块', 'url' => ['/test/index']],
                ],
            ]
        ) ?>
        <?php endif; ?>
    </section>

</aside>
