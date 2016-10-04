<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<?= '<link href="backend/views/layouts/mail.css" rel="stylesheet">';?>
<table border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">
            <table class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <p>Dear <?php echo Html::encode($toName) ?>,</p>
                        <p><?= 'Please find the lesson schedule for the program you enrolled on ' . Yii::$app->formatter->asDate($model->course->startDate) ?> </p>
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                          <tbody>
                            <tr>
                              <td align="left">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td><strong><?= 'Teacher Name: ' ?></strong> <?= $model->course->teacher->publicIdentity; ?></td>
                                                <td><strong><?= 'Program Name: ' ?></strong> <?= $model->course->program->name; ?></td>
                                                <td><strong><?= 'Time: ' ?></strong> 
                                                    <?php 
                                                        $fromTime = \DateTime::createFromFormat('H:i:s', $model->course->fromTime);
                                                        echo $fromTime->format('h:i A');
                                                    ?>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong><?= 'Durartion: ' ?></strong>
                                                    <?php 
                                                        $length = \DateTime::createFromFormat('H:i:s', $model->course->duration);
                                                        echo $length->format('H:i'); 
                                                    ?>
                                                </td>
                                                <td><strong><?= 'Start Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->course->startDate); ?></td>
                                                <td><strong><?= 'End Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->course->endDate); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>  

                                    <h4><strong><?= 'Schedule of Lessons' ?> </strong></h4> 

                                    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
                                        <?php echo GridView::widget([
                                        'dataProvider' => $lessonDataProvider,      
                                        'tableOptions' =>['class' => 'table table-bordered'],
                                        'headerRowOptions' => ['class' => 'bg-light-gray' ],
                                        'summary' => '',
                                        'columns' => [            
                                            [               
                                                'value' => function($data) {
                                                    $lessonDate =  \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                                                    $date = $lessonDate->format('l, F jS, Y @ g:i a');    
                                                    return ! empty($date) ? $date : null;
                                                },
                                            ],          
                                        ],
                                        ]); ?>
                                    <?php yii\widgets\Pjax::end(); ?>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <p>Thank you</p>
                        <p>Arcadia Music Academy Team.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- END MAIN CONTENT AREA -->
              </table>

            <!-- START FOOTER -->
            <div class="footer">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block">
                    <span class="apple-link">Company Inc, 3 Abbey Road, San Francisco CA 94102</span>
                    <br> Don't like these emails? <a href="http://i.imgur.com/CScmqnj.gif">Unsubscribe</a>.
                  </td>
                </tr>
                <tr>
                  <td class="content-block powered-by">
                    Powered by <a href="http://htmlemail.io">HTMLemail</a>.
                  </td>
                </tr>
              </table>
            </div>

            <!-- END FOOTER -->
            
<!-- END CENTERED WHITE CONTAINER --></div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>