<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
?>
<?php Pjax::Begin(['id' => 'customer-add-listing', 'timeout' => 6000]); ?>
<table class="table table-condensed"><thead>
<tr class="bg-light-gray"><th>First Name</th><th>Last Name</th><th>E-mail</th><th>Phone</th><th class="action-column">&nbsp;</th></tr>
<tr id="w0-filters" class="filters"><td><input type="text" class="form-control customer-add-firstname" name="UserSearch[firstname]"></td><td><input type="text" class="form-control customer-add-lastname" name="UserSearch[lastname]"></td><td><input type="text" class="form-control customer-add-email" name="UserSearch[email]"></td><td>&nbsp;</td><td>&nbsp;</td></tr>
    </thead></tbody></table>
 <?= GridView::widget([
            'dataProvider' => $userDataProvider,
            'summary' =>false,
            'tableOptions' => ['class' => 'table table-condensed'],
            'headerRowOptions' => ['class' => 'bg-light-gray invisible'],
            'columns' => [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                },
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                },
            ],
            'email',
            [
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
            ],
            	[
			'class' => 'yii\grid\ActionColumn',
			'contentOptions' => ['style' => 'width:50px'],
			'template' => '{view}',
			'buttons' => [
				'view' => function ($url, $userModel) use($model) {
					$url = Url::to(['invoice/update-customer', 'id' => $model->id]);
					return Html::a('Add', $url, ['class' => 'add-customer-invoice','id' => $userModel->id ]);
				},
			]
        ],        
        ],
    ]); ?>
<?php Pjax::end(); ?>
<script>
	$(document).on('change keyup paste', '.customer-add-firstname', function (e) {
		var firstName = $('.customer-add-firstname').val();
		var id = '<?= $model->id;?>';
		var params = $.param({'id' : id, 'firstName' : firstName});
		$.ajax({
            url    : '<?= Url::to(['invoice/fetch-user']); ?>?' + params,
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
               if(response.status) {
				   $('#invoice-customer-modal .modal-body').html(response.data);
			   }
            }
        });
		return false;
	});
</script>