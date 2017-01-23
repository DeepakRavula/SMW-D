<?php

use common\models\CustomerDiscount;

?>
<div>
<?php
echo $this->render('_form-discount', [
	'model' => new CustomerDiscount(),
	'userModel' => $model,
])
?>
</div>
