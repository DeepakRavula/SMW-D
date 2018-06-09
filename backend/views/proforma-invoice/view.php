<?php

use backend\models\search\InvoiceSearch;
use common\models\InvoiceLineItem;
use common\models\Note;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use common\models\UserProfile;
use common\models\UserEmail;
use yii\imperavi\TableImperaviRedactorPluginAsset;
TableImperaviRedactorPluginAsset::register($this);
use kartik\select2\Select2Asset;
Select2Asset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
