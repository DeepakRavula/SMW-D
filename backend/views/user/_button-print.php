<?php 
    use common\models\User;
    use yii\helpers\Html; ?>
<div style="margin-top:-5px">
<?php if ($searchModel->role_name === User::ROLE_CUSTOMER):?>
    <?= Html::a('<i title="Print" class="fa fa-print btn btn-default btn-lg"></i>', ['print/user?UserSearch%5Brole_name%5D=customer'], ['class' => 'btn btn-box-tool', 'target' => '_blank']) ?>
 <?php endif; ?>
    <?php if ($searchModel->role_name === User::ROLE_TEACHER):?>
    <?= Html::a('<i title="Print" class="fa fa-print btn btn-default btn-lg"></i>', ['print/user?UserSearch%5Brole_name%5D=teacher'], ['class' => 'btn btn-box-tool', 'target' => '_blank']) ?>
 <?php endif; ?>
</div>