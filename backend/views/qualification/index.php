<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\QualificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qualifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qualification-index">

    <p>
        <?php echo Html::a('Create Qualification', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => 'Serial No.',
            ],
            [
                'label' => 'Teacher Name',
                'value' => function ($data) {
                    return !empty($data->user->userProfile->fullName) ? $data->user->userProfile->fullName : null;
                },
            ],
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->program->name) ? $data->program->name : null;
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
