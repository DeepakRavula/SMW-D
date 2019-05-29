<?php 
$this->title = $model->publicIdentity;
if (!$isPrintView) {
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['show-all'] = $this->render('_button', [
    'model' => $model,
   ]);
} else {  
    echo $model->publicIdentity; 
}
?>
<div class="m-b-25"> </div>
<div class="row">
    <div class = col-md-12>
<?= $this->render('_outstanding-invoice', [
                'outstandingInvoiceDataProvider' => $outstandingInvoice,
                'userModel' => $model,
            ]); ?>
            </div>
</div>      
<div class="row">  
<div class = col-md-12>    
<?= $this->render('_pre-paid-lessons', [
                'prePaidLessonsDataProvider' => $prePaidLessons,
            ]); ?>
            </div>
</div>
<div class="row">
<div class = col-md-12>
<?= $this->render('_credits-available', [
                'creditsDataProvider' => $unUsedCredits,
            ]); ?>
            </div>
            </div>
<script>
    $(document).ready(function () {
        var isPrintView = <?= $isPrintView ?>;
        if (isPrintView) {
        window.print();
        }
    });
    </script>