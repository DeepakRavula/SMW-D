<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Programs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index m-t-20">
<div class="pull-right  m-r-20">
    <?= Html::checkbox('active', true, ['label' => 'Active Only', 'id' => 'active' ]); ?>
</div>

    <?php \yii\widgets\Pjax::begin(['id' => 'programIndex']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class'=>'col-md-5'],
            'tableOptions' =>['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                $u= \yii\helpers\StringHelper::basename(get_class($model));
                $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
                return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
            },
            'columns' => [
                'name',
                'rate:currency',

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
    <div class="clearfix"></div>
	<div class="col-md-12 m-b-20">
        <?php echo Html::a('Add', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

</div>
<script>
$("#active").on("click", function() {
    var active=0;
    if ($(this).is(":checked"))
    {
      active = 1;
    }
    var url = "<?php echo Url::toRoute('/program/index?active=')?>"+active;
    $.ajax({
        url: url,
        type:'POST',
        success:function(result){
            $.pjax.reload({container:"#programIndex"});  //Reload GridView
        },
    });
           
});
</script>
