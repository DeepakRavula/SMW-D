<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Daily Schedule';
?>

<div class="payments-index p-10">
    <?php echo $this->render('_publiclist', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
	
</div>
