<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;
use yii\helpers\ArrayHelper;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
    <div class="wrapper">
      <?= $this->render('_header'); ?>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
              <?= $this->render('_left-menu', [
				  
			  ]); ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php if (isset($this->params['goback'])) : ?>  
                    <div class="pull-left m-r-10">
                        <?php echo $this->params['goback']; ?>
                    </div>
                    <?php endif; ?>
                    <?php echo $this->title ?>
                    
                    <?php if (isset($this->params['action-button'])) : ?>
                        
                        <div class="pull-right m-r-10">
                            <?php echo $this->params['action-button']; ?>
                        </div>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                </h1>

            </section>

            <!-- Main content -->
            <section class="content">
                <?php if (Yii::$app->session->hasFlash('alert')):?>
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ])?>
                <?php endif; ?>
                <?php echo $content ?>
            </section><!-- /.content -->           
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php $this->endContent(); ?>
