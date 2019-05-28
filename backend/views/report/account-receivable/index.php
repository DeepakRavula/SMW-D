<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Accounts Receivable';
?>
<?php echo $this->render('account-receivable', ['dataProvider' => $dataProvider,]); ?>