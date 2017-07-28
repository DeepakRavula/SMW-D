<?php

use yii\helpers\Url;
use backend\assets\CustomGridAsset;
CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;

$this->title = 'Discount Report';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="payments-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<?php echo $this->render('_discount', ['dataProvider' => $dataProvider]); ?>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<style type="text/css" src="/admin/css/group-grid.css"></style>

<script>
    $(document).on("click", "#print", function() {
        var dateRange = $('#discountsearch-daterange').val();
        var params = $.param({ 'DiscountSearch[dateRange]': dateRange });
        var url = '<?php echo Url::to(['report/discount-print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>