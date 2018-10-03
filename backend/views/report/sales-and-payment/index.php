<?php
/* @var $this yii\web\View */

use common\models\Location;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\TaxCode;
use common\models\TaxType;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

$this->title = 'Sales and Payments Summary';
//$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);

?>
<div class="col-xs-12 col-md-6 form-group form-inline">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">	
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Sales',
        'withBorder' => true,
    ]);
    echo $this->render('_sales', [
        'searchModel' => $searchModel, 
        'salesDataProvider' => $salesDataProvider,
    ]);
    ?>
    
<?php LteBox::end() ?>
</div> 
<div class="col-md-12">	
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Payment',
        'withBorder' => true,
    ]);
    echo $this->render('_payment', [
        'searchModel' => $searchModel, 
        'paymentsDataProvider' => $paymentsDataProvider,
    ]);

    ?>
    
<?php LteBox::end() ?>
</div>
<div class="clearfix"></div>