<?php

use yii\bootstrap\Tabs;
use yii\bootstrap\Html;
use yii\helpers\Url;
use backend\models\search\CourseSearch;
use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pull-right calendar-filter">
    <?= Html::dropDownList('action', null, ['c'=>'Substitute Teacher'], [
        'prompt' => 'Select Bulk Action', 'class' => 'form-control',
        'id' => 'bulk-action',
        'url' => Url::to(['teacher-substitute/index'])
    ])?>
</div>
<div class="nav-tabs-custom">
<?php 

$indexPrivateLesson = $this->render('_index-lesson', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

$courseSearchModel = new CourseSearch();
$dataProvider = $courseSearchModel->search(Yii::$app->request->queryParams);

$indexGroupLesson = $this->render('/course/index', [
	'searchModel' => $courseSearchModel,
	'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Private',
           'content' => $indexPrivateLesson,
            'options' => [
				'id' => 'private',
			],
        ],
        [
            'label' => 'Group',
            'content' => $indexGroupLesson,
            'options' => [
				'id' => 'group',
			],
        ],
    ],
]); ?>
<div class="clearfix"></div>
</div>
