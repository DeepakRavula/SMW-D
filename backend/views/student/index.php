<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
	.advanced-search {
        display: none;
    }
		.search-mode {
        display: block;
    }
</style>

<div class="student-index">
<?php yii\widgets\Pjax::begin(['id' => 'student-index']); ?>
    <?php echo $this->render('_search', ['model' => $searchModel,'searchMode'=> $searchMode]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
            [
				'class' => 'yii\grid\SerialColumn',
				'header' => 'Serial No.',
			],
			'first_name',
            'last_name',
            'birth_date:date',
			[
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = ! (empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>

	

</div>
<script>
		$(".advanced-search-toggle").click(function(){
		if($('.search-mode').is(":visible")){
			$('.advanced-search').fadeOut();
		} else {
			$('.advanced-search').fadeIn();
		}
	});
	</script>