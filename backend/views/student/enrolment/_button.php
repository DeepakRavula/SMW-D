<?php

use yii\helpers\Url;

?>
<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
	<li><a id="add-private-enrol" href="#">Add Private...</a></li>
	<li><a id="add-group-enrol" href="<?= Url::to(['course/fetch-group', 'studentId' => $model->id, 'courseName' => null]); ?>">Add Group...</a></li>
</ul>