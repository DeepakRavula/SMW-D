<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\User;
use common\models\Program;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$title = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Private Progam' : 'Group Progam';
$this->title = ucwords($model->name).'-'.$title;
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'ProgramSearch[type]' => $model->type], ['class' => 'go-back']);
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($roles as $name => $description) {
    $role = $name;
}
?>
<div class="program-view">
    <?php yii\widgets\Pjax::begin([
        'id' => 'program-details',
    ]) ?>
	<div class="row-fluid user-details-wrapper">
    <div class="col-xs-2 p-l-0">
        	<i class="fa fa-music"></i> <?php echo $model->name; ?>
    </div>
	<?php $rate = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour($)' : 'Rate Per Course($)'; ?>
	<div class="col-xs-2 p-l-0" data-toggle="tooltip" data-placement="bottom" title= "<?= $rate; ?>" >
		<i class="fa fa-money"></i> <?php echo $model->rate; ?>
	</div>
    <div class="clearfix"></div>
    <?php if ($role === User::ROLE_ADMINISTRATOR):?>
        <div class="col-xs-2 m-t-15 p-l-0">
            <?php echo Html::a('<i class="fa fa-pencil" aria-hidden="true"></i> Edit', '#', [
                            'id' => 'edit-program-button',
                            'class' => 'edit-program-button m-l-20'
                        ]); ?>
        </div>
        <div class="col-xs-2 m-t-15 p-l-0">
            <?php
                echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                    'class' => '',
                    'data' => [
                        'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <div class="clearfix"></div>
        </div>
    <?php
        Modal::begin([
            'header' => '<h4 class="m-0">Edit program</h4>',
            'id'=>'program-modal',
        ]);
         echo $this->render('_form', [
                'model' => $model,
                ]);
        Modal::end();
        ?>		
        <?php endif; ?> 
    <?php \yii\widgets\Pjax::end(); ?>
    </div>
    <div class="clearfix"></div>
</div>
<div class="nav-tabs-custom">
		<?php
            $studentContent = $this->render('_student', [
                'model' => $model,
                'studentDataProvider' => $studentDataProvider,
            ]);
            $teacherContent = $this->render('_teacher', [
                'model' => $model,
                'teacherDataProvider' => $teacherDataProvider,
            ]);
        ?>
		<?php
        $items = [
            [
                'label' => 'Students',
                'content' => $studentContent,
                'options' => [
                      'id' => 'student',
                  ],
                'active' => true,
            ],
            [
                'label' => 'Teachers',
                'content' => $teacherContent,
                'options' => [
                      'id' => 'teacher',
                  ],
            ],
        ];
        ?>
		<?php
        echo Tabs::widget([
            'items' => $items,
        ]);
        ?>
		<div class="clearfix"></div>
</div>
    <script>
       $(document).on('click', '.edit-program-button', function () {
			$('#program-modal').modal('show');
			return false;
		});
        $(document).on('click', '.program-edit-cancel-button', function () {
			$('#program-modal').modal('hide');
		});
    $(document).on('beforeSubmit', '#program-form', function (e) {
            var programId = <?= $model->id; ?>;
            var url = '<?= Url::to(['program/update']);?>?id=' + programId;
           
            $.ajax({
                url: url,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#program-details', timeout: 6000});
                        $('#program-modal').modal('hide');
                    } else
                    {
                        $('#program-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
            return false;
        });
    
    </script>
