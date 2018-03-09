
<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SMW Latest Features and Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-index">
	<div class="content">
		<?php echo ListView::widget([
                'dataProvider' => $dataProvider,
               'itemView' => '_view-blog',
          ]); ?>
</div>
</div>