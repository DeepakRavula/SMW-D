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
            Html::checkbox(
                'show-all',
                false,
                ['id' => 'show-all-programs', 'class' => 'show-all-private-programs']
            ),
            Html::label('Show All', '', ['id' => 'show-all-programs-label'])
        ],
        'title' => 'Private Programs',
        'withBorder' => true,
    ])
    ?>
    <?php Pjax::begin(['id' => 'private-program-listing', 'enablePushState' => false]) ?>
    <?php
    echo GridView::widget([
        'id' => 'private-program-grid',
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
                'label' => 'Rate Per Hour',
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
        $(".show-all-private-programs").on("click", function () {
            var showAllPrograms = $(this).is(":checked");
            var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[programType]=' + "<?php echo Program::TYPE_PRIVATE_PROGRAM; ?>";
            $.pjax.reload({url: url, container: "#private-program-listing", replace: false, timeout: 4000});  //Reload GridView
        });
    });
</script>
