<?php
/* @var $this \yii\web\View */

/* @var $content string */

$this->beginContent('@frontend/views/layouts/common.php')
?>
<link rel="icon" href="<?php echo env('SITE_URL'); ?>/arcadia-fav.png" type="image/png">
<link rel='shortcut icon' type='image/x-icon' href="<?php echo env('SITE_URL'); ?>/favicon.ico" />
<?php echo $content ?>
<?php $this->endContent() ?>