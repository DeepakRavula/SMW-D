<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use common\models\Program;
use yii\grid\GridView;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles    = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
?>


<div>
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => [
            '<i title="Add" class="fa fa-plus" id = "add-program"></i>',
            Html::checkbox('show-all', false,
                ['id' => 'show-all-programs', 'class' => 'show-all-group-programs']),
            Html::label('Show All', '', ['id' => 'show-all-programs-label'])
        ],
        'title' => 'Group Programs',
        'withBorder' => true,
    ])
    ?>
    <?php Pjax::begin(['id' => 'group-program-listing', 'enablePushState' => false]) ?>
    <?php
    echo GridView::widget([
        'id' => 'group-program-grid',
        'dataProvider' => $privateDataProvider,
        'filterModel' => $searchModel,
        //'condensed' => true,
        //'hover' => true,
        'columns' => [
            [
                'attribute' => 'name',
                'contentOptions' => ['style' => 'width:250px;'],
                'value' => function ($data) {
                    return $data->name;
                },
            ],
            [
                'label' => 'Rate Per Course',
                'attribute' => 'rate',
                'format' => 'currency',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'style' => 'width:100px;'],
                'value' => function ($data) {
                    return !empty($data->rate) ? $data->rate : null;
                },
            ],
        ],
    ]);
    ?>
<?php Pjax::end(); ?>
<?php LteBox::end() ?>
    <div class="clearfix"></div>
</div>
<script>
    $(document).ready(function () {
        $(".show-all-group-programs").on("click", function () {
            var showAllPrograms = $(this).is(":checked");
            var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[programType]=' + "<?php echo Program::TYPE_GROUP_PROGRAM; ?>";
            $.pjax.reload({url: url, container: "#group-program-listing", replace: false, timeout: 4000});  //Reload GridView
        });
    });
</script>