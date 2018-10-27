<?php

namespace backend\controllers;

use Yii;
use common\models\PrivateLesson;
use backend\models\search\PrivateLessonSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use common\models\Lesson;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use backend\models\lesson\discount\LessonMultiDiscount;
use common\models\ClassroomUnavailability;
use common\models\EditClassroom;
/**
 * PrivateLessonController implements the CRUD actions for PrivateLesson model.
 */
class PrivateLessonController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => [
                    'merge', 'update-attendance', 'delete', 'apply-discount','edit-duration', 'edit-classroom', 
                ],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index', 'update', 'view', 'delete', 'create', 'split', 'merge', 'update-attendance',
                                'apply-discount','edit-duration', 'edit-classroom'
                        ],
                        'roles' => ['managePrivateLessons'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all PrivateLesson models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrivateLessonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PrivateLesson model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PrivateLesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PrivateLesson();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PrivateLesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PrivateLesson model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete()
    {
        $lessonIds = Yii::$app->request->get('PrivateLesson')['ids'];
        $isBulk = Yii::$app->request->get('PrivateLesson')['isBulk'];
        $lessons = Lesson::find()
            ->andWhere(['id' => $lessonIds])
            ->orderBy(['date' => SORT_DESC])
            ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->isDeletable()) {
                return [
                    'status' => false,
                    'message' => 'You can\'t delete this lesson.',
                ];
            }
        }
        foreach ($lessons as $lesson) {
            $message = 'Lesson has been deleted successfully!';
            if ($lesson->hasLessonCredit($lesson->enrolment->id)) {
                $message .= ' Lesson credits transfered to customer account';
            }
            $lesson->delete();
            $response = [
                'status' => true,
                'url' => Url::to(['lesson/index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON]),
                'message' => $message
            ];
        }

        return $response;
    }
    public function actionEditDuration()
    {
        $lessonIds = Yii::$app->request->get('PrivateLesson')['ids'];
        $lessonId = end($lessonIds);
      
        foreach ($lessonIds as $lessonId) {
            $model = $this->findModel($lessonId);
            if(!$model->isEditable()){
                return [
                    'status' => false,
                    'message' => ' One of the chosen lesson is invoiced. You can\'t edit duration for this lessons',
                ]; 
            }
        }
        $model = new Lesson();
        $data = $this->renderAjax('_form-edit-duration', [
            'lessonIds' => $lessonIds,
            'model' => $model,
            
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            foreach ($lessonIds as $lessonId) {
                $model = $this->findModel($lessonId);
                $model->load($post);
                $model->save();
            }
            $response = [
                'status' => true,
                'message' => 'Lesson Duration Edited Sucessfully',
            ];
        } else {
              $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionSplit($id)
    {
        $model = $this->findModel($id);
        if (!$model->canExplode()) {
            return false;
        }
        $model->privateLesson->split();
        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'The Lesson has been exploded successfully.',
        ]);
        return $this->redirect(['student/view', 'id' => $model->enrolment->student->id, '#'=> 'unscheduledLesson']);
    }
    
    public function actionMerge($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Lesson::SCENARIO_MERGE);
        $post = Yii::$app->request->post();
        $additionalDuration = new \DateTime(Lesson::DEFAULT_MERGE_DURATION);
        $lessonDuration = new \DateTime($model->duration);
        $lessonDuration->add(new \DateInterval('PT' . $additionalDuration->format('H')
            . 'H' . $additionalDuration->format('i') . 'M'));
        $model->duration = $lessonDuration->format('H:i:s');
        $model->save();
        $splitLesson = $this->findModel($post['radioButtonSelection']);
        $model->splittedLessonId = $splitLesson->id;
        if ($model->validate()) {
            $splitLesson->privateLesson->merge($model);
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'The Lesson has been extended successfully.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'error' => end($errors),
                'status' => false
            ];
        }
    }
    
    public function actionUpdateAttendance($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            return [
                'status' => true,
            ];
        }
    }
    /**
     * Finds the PrivateLesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return PrivateLesson the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lesson::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionApplyDiscount()
    {
        $lessonIds = Yii::$app->request->get('PrivateLesson')['ids'];
        $lessonId = end($lessonIds);
        $model = $this->findModel($lessonId);
        foreach ($lessonIds as $lessonId) {
            $model = $this->findModel($lessonId);
            if(!$model->isEditable()){
                return [
                    'status' => false,
                    'message' => ' One of the chosen lesson is invoiced. You can\'t edit discount for this lessons',
                ]; 
            }
        }
        $lineItemDiscount = LessonMultiDiscount::loadLineItemDiscount($lessonIds);
        $paymentFrequencyDiscount = LessonMultiDiscount::loadPaymentFrequencyDiscount($lessonIds);
        $customerDiscount = LessonMultiDiscount::loadCustomerDiscount($lessonIds);
        $multiEnrolmentDiscount = LessonMultiDiscount::loadEnrolmentDiscount($lessonIds);
        $data = $this->renderAjax('_form-apply-discount', [
            'lessonIds' => $lessonIds,
            'model' => $model,
            'customerDiscount' => $customerDiscount,
            'paymentFrequencyDiscount' => $paymentFrequencyDiscount,
            'lineItemDiscount' => $lineItemDiscount,
            'multiEnrolmentDiscount' => $multiEnrolmentDiscount
        ]);
        $post = Yii::$app->request->post();
        if ($post) {
            foreach ($lessonIds as $lessonId) {
                $model = $this->findModel($lessonId);
                $lineItemDiscount = LessonMultiDiscount::loadLineItemDiscount([$lessonId]);
                $customerDiscount = LessonMultiDiscount::loadCustomerDiscount([$lessonId]);
                $lineItemDiscount->load($post);
                $customerDiscount->load($post);
                $lineItemDiscount->save();
                $customerDiscount->save();
                $paymentFrequencyDiscount = LessonMultiDiscount::loadPaymentFrequencyDiscount([$lessonId]);
                $multiEnrolmentDiscount = LessonMultiDiscount::loadEnrolmentDiscount([$lessonId]);
                $paymentFrequencyDiscount->load($post);
                $multiEnrolmentDiscount->load($post);
                $paymentFrequencyDiscount->save();
                $multiEnrolmentDiscount->save();
            }
            $model->save();
            $response = [
                'status' => true
            ];
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionEditClassroom()
    {
        $editClassroomModel = new EditClassroom();
        $editClassroomModel->load(Yii::$app->request->get());
        $post = Yii::$app->request->post();
        if ($post) {
            $editClassroomModel->setScenario(EditClassroom::SCENARIO_EDIT_CLASSROOM);
            if ($editClassroomModel->load($post) && $editClassroomModel->validate()) {
                foreach ($editClassroomModel->lessonIds as $lessonId) {
                    $model = $this->findModel($lessonId);
                    $model->classroomId = $editClassroomModel->classroomId;
                    $model->save();
                }
                $response = [
                    'status' => true,   
                    'message' => 'Lesson Classroom Edited Sucessfully',
                ];
            } else {
                $response = [
                    'status' => false,
                    'error' => $editClassroomModel->getErrors('lessonIds','classroomId'),
                ];
            }
        } else {
            $editClassroomModel->setScenario(EditClassroom::SCENARIO_BEFORE_EDIT_CLASSROOM);
            if ($editClassroomModel->validate()) {
                $data = $this->renderAjax('_form-edit-classroom', [
                    'model' => $editClassroomModel,
                ]);
                $response = [
                    'status' => true,
                    'data' => $data
                ];
            } else {
                $response = [
                    'status' => false,
                    'error' => $editClassroomModel->getErrors('lessonIds'),
                ];
            }
        }
        return $response;
    }
}
