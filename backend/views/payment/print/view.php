<?php
   use yii\widgets\Pjax; 
   use common\models\Location;
/* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Proforma-Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<?php
   echo $this->render('/print/_invoice-header', [
       'paymentModel'=>$model,
       'userModel'=>$model->user,
       'locationModel'=>$model->user->location->location,
]);
   ?>

<div class="row-fluid invoice-info m-t-10">

<?php $lessonCount = $lessonDataProvider->getCount(); ?>
    <?php if ($lessonCount > 0) : ?>
    <div class="col-xs-10">
        <div class="m-l-22"> <b>Lessons</b></div>
    <?=
    $this->render('/payment/_lesson-line-item', [
        'model' => $model,
        'canEdit' => false,
        'lessonDataProvider' => $lessonDataProvider,
    ]);
    ?>
   </div>
    <?php endif; ?>
    <?php $lessonCount = $groupLessonDataProvider->getCount(); ?>
    <?php if ($lessonCount > 0) : ?>
    <div class="col-xs-10">
        <div class="m-l-22"> <b>Group Lessons</b></div>
    <?=
    $this->render('/payment/_group-lesson-line-item', [
        'model' => $model,
        'canEdit' => false,
        'lessonDataProvider' => $groupLessonDataProvider,
    ]);
    ?>
   </div>
    <?php endif; ?>
    <?php $invoiceCount = $invoiceDataProvider->getCount(); ?>
        <?php if ($invoiceCount > 0) : ?>
    <div class="col-xs-10">
        <div class="m-l-22"> <b>Invoices</b></div>
	<?=
	$this->render('/payment/_invoice-line-item', [
	    'model' => $model,
	    'canEdit' => false,
	    'invoiceDataProvider' => $invoiceDataProvider,
	]);
	?>
	</div>
    <?php endif; ?>
    <div class="col-xs-10">
        <div class="m-l-22"> <b>Payments Used</b></div>
	<?=
	$this->render('/payment/_credits-available', [
	    'model' => $model,
	]);
	?>
	</div>
</div>
<script>
    $(document).ready(function() {
      window.print();
    });
</script>
