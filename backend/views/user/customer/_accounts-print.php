<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\Location;

?>
<?php
   echo $this->render('/print/_header', [
       'userModel'=>$model,
       'locationModel'=>$model->userLocation->location,
]);
   ?>

<div class="col-md-12 p-b-20">
        <?php
        echo GridView::widget([
            'dataProvider' => $accountDataProvider,
            'summary' => false,
            'emptyText' => false,
            'tableOptions' => ['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
                    [
                    'label' => 'Date',
                    'value' => function ($data) {
                        return Yii::$app->formatter->asDate($data->date);
                    }
                ],
                    [
                    'headerOptions' => ['class' => 'text-left'],
                    'contentOptions' => ['class' => 'text-left'],
                    'label' => 'Description',
                    'value' => function ($data) {
                        return $data->getAccountDescription();
                    }
                ],
                    [
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'label' => 'Debit',
                    'value' => function ($data) {
                        return !empty($data->debit) ? Yii::$app->formatter->asCurrency($data->debit) : null;
                    }
                ],
                    [
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'label' => 'Credit',
                    'value' => function ($data) {
                        return !empty($data->credit) ? Yii::$app->formatter->asCurrency(abs($data->credit)) : null;
                    }
                ],
                    [
                    'format' => ['decimal', 2],
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'label' => 'Balance',
                    'value' => function ($data) {
                        return $data->balance;
                    }
                ]
            ],
        ]);

        ?>
    </div>
    <script>
        $(document).ready(function () {
            window.print();
        });
    </script>
