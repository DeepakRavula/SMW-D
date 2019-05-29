<?= $this->render('_outstanding-invoice', [
                'outstandingInvoiceDataProvider' => $outstandingInvoice,
                'userModel' => $model,
            ]); ?>
<?= $this->render('_pre-paid-lessons', [
                'prePaidLessonsDataProvider' => $prePaidLessons,
            ]); ?>
            <?= $this->render('_credits-available', [
                'creditsDataProvider' => $unUsedCredits,
            ]); ?>