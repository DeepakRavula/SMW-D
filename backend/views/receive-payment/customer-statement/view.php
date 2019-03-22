<?php
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Location;
use common\models\User;
use yii\bootstrap\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
   
    <?php if ($lessonCount > 0) : ?>
    <div class="col-xs-10">
                <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
        <?= $this->render('/receive-payment/print/_lesson-line-item', [
            'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
            'searchModel' => $searchModel,
        ]); ?>   
    </div>     
    <?php endif; ?> 

   <script>
        $(document).ready(function() {
            window.print();
        });
    </script>   