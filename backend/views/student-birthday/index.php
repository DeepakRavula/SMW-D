<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Birthdays';

?>

<div class="payments-index p-10">
    
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<?php echo $this->render('_birthday', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

</div>

