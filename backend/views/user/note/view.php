<div class="user-note-content p-10">
<?=
    $this->render('_view', [
        'noteDataProvider' =>  $noteDataProvider,
        'model' => $model,
]);
?>
</div>
