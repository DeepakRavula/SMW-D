<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\Location;

?>

<div class="invoice-view p-10">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <span class="logo-lg"><b>Arcadia</b>SMW</span>
                <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <?php
            $locationId = Yii::$app->session->get('location_id');
            $location = Location::findOne(['id' => $locationId]);

            ?>
            From
            <address>
                <strong> 
                <?php if (!empty($location->name)): ?>
                    <?= $location->name ?>
                <?php endif; ?>
                </strong><br>
                <?php if (!empty($location->address)): ?>
                    <?= $location->address ?>
                <?php endif; ?><br>
                <?php if (!empty($location->city->name)): ?>
                    <?= $location->city->name ?>,
                <?php endif; ?>
                    <?php if (!empty($location->province->name)): ?>
                    <?= $location->province->name ?>
                <?php endif; ?><br>
                <?php if (!empty($location->postal_code)): ?>
                    <?= $location->postal_code ?>
                <?php endif; ?><br>
                <?php if (!empty($location->phone_number)): ?>
                    Phone:<?= $location->phone_number ?>
                <?php endif; ?><br>
                <?php if (!empty($location->email)): ?>
                    E-mail:<?= $location->email ?>
                <?php endif; ?>
            </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
            To
            <address>
                <strong><?php echo isset($model->publicIdentity) ? $model->publicIdentity : null ?></strong><br>
                <?php
                $addresses = $model->addresses;
                foreach ($addresses as $address) {
                    if ($address->label === 'Billing') {
                        $billingAddress = $address;
                        break;
                    }
                }
                $phoneNumber = $model->phoneNumber;
                ?>
                <!-- Billing address -->
                <?php if (!empty($billingAddress)) {

                    ?>
                    <?php
                    echo $billingAddress->address . '<br> ' . $billingAddress->city->name . ', ';
                    echo $billingAddress->province->name . '<br>';
                    echo $billingAddress->postal_code . '<br/>';
                }

                ?>
                <?php if (!empty($phoneNumber)): ?>
                    Phone:<?= $phoneNumber->number ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->email)): ?>
                    E-mail:<?= $model->email ?>
<?php endif; ?>
            </address>
        </div>
    </div>
    <div class="col-md-12 p-b-20">
        <h5><strong><?= 'Accounts' ?> </strong></h5> 
        <?php
        echo GridView::widget([
            'dataProvider' => $accountDataProvider,
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
                        return !empty($data->credit) ? Yii::$app->formatter->asCurrency($data->credit) : null;
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
