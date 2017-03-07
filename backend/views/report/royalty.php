<?php
/* @var $this yii\web\View */

use common\models\Location;
use yii\helpers\Url;

$this->title = 'Royalty';
?>
    <div class="col-xs-12 col-md-6 p-10 p-r-0">
		<?php echo $this->render('_search', ['model' => $searchModel]); ?>
	</div>
	<div class="clearfix"></div>
<?php yii\widgets\Pjax::begin([
	'id' => 'royalty',
    'timeout' => 6000,
]) ?>
	<?php if(!$searchModel->summarizeResults) : ?>
	<?= $this->render('royalty-detail',[
		'searchModel' => $searchModel, 
		'royaltyDataProvider' => $royaltyDataProvider
	]); ?>
	<?php else : ?>
	<?= $this->render('royalty-summarize',[
		'searchModel' => $searchModel, 
		'invoiceTaxTotal' => $invoiceTaxTotal,
		'payments' => $payments,
		'royaltyPayment' => $royaltyPayment
	]); ?>
	<?php endif; ?>
<?php \yii\widgets\Pjax::end(); ?>
<script>
$(document).ready(function () {
	$("#royaltysearch-summarizeresults").on("change", function() {
		var summariesOnly = $(this).is(":checked");
		var dateRage = $('#royaltysearch-daterange').val();
		var params = $.param({ 'RoyaltySearch[summarizeResults]': (summariesOnly | 0),
			'RoyaltySearch[dateRange]': dateRage});
		var url = '<?php echo Url::to(['report/royalty']); ?>?' + params;
		$.pjax.reload({url:url,container:"#royalty",replace:false,  timeout: 4000});  
    });
});
</script>
