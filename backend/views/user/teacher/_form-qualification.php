<div class="p-10"><h4>Qualifications</h4></div>
<div id = "qualification-section" class="section-tab">
	<?php
    echo $this->render('_form-private-qualification', [
        'model' => $model,
        'form' => $form,
		'privateQualificationModels' => $privateQualificationModels,
    ]);
    ?>
	<?php
    echo $this->render('_form-group-qualification', [
        'model' => $model,
        'form' => $form,
		'groupQualificationModels' => $groupQualificationModels,
    ]);
    ?>
</div>
