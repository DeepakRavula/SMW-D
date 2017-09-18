<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Tax */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Taxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($roles as $name => $description) {
    $role = $name;
}
?>
<div class="tax-view">

	<?php if ($role === User::ROLE_ADMINISTRATOR): ?>
    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
	<?php endif; ?>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
                            [
                'label' => 'Province Name',
                'value' => !(empty($model->province->name)) ? $model->province->name : null,
            ],
            'tax_rate',
            'since',
        ],
    ]) ?>

</div>
