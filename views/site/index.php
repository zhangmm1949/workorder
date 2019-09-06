<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h2>基本说明</h2>

        <h2 class="lead">这是一个非常简单的工单管理系统</h2>
        <h3 class="lead">为了保持简单易用原则 请使用者及后来开发者 尽量不要打破以下规则：</h3>

    </div>
    <h4><b>1. 角色决定权限， 系统决定范围</b></h4>
    <p>系统仅提供 管理组 与 业务组 两个角色，管理组比业务组多的是对系统的部分设定，例如添加系统，修改用户信息等</p>
    <p>用户可以看到哪些工单，由分配给用户的系统决定。分配了相同的系统，不管是管理组还是业务组，所见范围一致（当然，管理组比用户组多了删除工单的权限）。</p>
    <h4><b>2. 工单状态尽量不要新加</b></h4>
    <p>目前工单状态有 待处理、处理中、已完成 三个。新建工单即待处理，有人跟进即可改为处理中，结束即可改为已完成。如果处理过程中发现是由其他系统引起，或者另一个问题引起，请结束当前工单，并创建新的工单记录发现的新问题。</p>

    <div class="body-content">

        <!--<div class="row">
            <div class="col-lg-4">
                <?php
/*                    foreach ($tags as $item){
                        echo "<p>$item[tag] -- $item[num]</p>";
                    }
                */?>
            </div>
        </div>-->

    </div>
</div>
