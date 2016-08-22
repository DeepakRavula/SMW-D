<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TaxCode */

$this->title = 'Create Tax Code';
$this->params['breadcrumbs'][] = ['label' => 'Tax Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-code-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
