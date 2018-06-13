<div class="invoice-note-content">
<?=
    $this->render('_view', [
        'noteDataProvider' =>  $noteDataProvider,
        'model' => $model,
]);
?>
</div>
