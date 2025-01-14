<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\PrivateLesson;
use common\models\Enrolment;
use common\models\Course;
use common\models\LessonReschedule;
use yii\data\ActiveDataProvider;
use backend\models\search\LessonSearchOld;
use yii\base\Model;
use common\models\Location;
use common\models\log\LogHistory;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Note;
use common\models\Student;
use yii\web\Response;
use common\models\Payment;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;
use yii\filters\ContentNegotiator;
use common\models\PaymentCycle;
use common\models\BulkRescheduleLesson;
use common\models\log\StudentLog;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\LessonHierarchy;
use common\models\LessonPayment;
use common\models\LessonReview;
use yii\helpers\ArrayHelper;
use common\models\LessonConfirm;
use common\models\LessonOwing;
use common\models\UnscheduleLesson;
use backend\models\search\LessonSearch;
use Carbon\Carbon;
use common\components\queue\EnrolmentConfirm;
use common\components\queue\LessonConfirm as QueueLessonConfirm;
use common\components\queue\MakeLessonAsRoot;
use common\models\NotificationEmailType;
use common\models\AutoEmailStatus;
use common\models\PrivateLessonEmailStatus;
use common\models\GroupLessonEmailStatus;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => [
                    'modify-classroom', 'merge', 'update-field',
                    'validate-on-update', 'modify-lesson', 'edit-classroom',
                    'payment', 'substitute', 'update', 'unschedule', 'credit-transfer',
                    'edit-price', 'edit-tax', 'edit-cost', 'fetch-conflict', 'edit-due-date',
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
                            'index', 'view', 'credit-transfer', 'validate-on-update', 'edit-price', ' edit-tax',
                            'fetch-duration', 'edit-classroom', 'update', 'update-field', 'review',
                            'fetch-conflict', 'confirm', 'invoice', 'modify-classroom',
                            'payment', 'substitute', 'unschedule', 'edit-cost', 'edit-tax', 'new-index', 'edit-due-date', 'index-new',
                            'index-old'
                        ],
                        'roles' => [
                            'managePrivateLessons',
                            'manageGroupLessons'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Lesson models.
     *
     * @return mixed
     */
    public function actionIndexOld()
    {
        $searchModel = new LessonSearchOld();
        $request = Yii::$app->request;
        $dataProvider = $searchModel->search($request->queryParams);
        $dataProvider->pagination->pageSize = 200;
        return $this->render('index-old', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionNewIndex()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $lessonOwingList = LessonOwing::find()->all();
        foreach ($lessonOwingList as $lessonOwing) {
            $lessonOwingIds[] = $lessonOwing->lessonId;
        }
        $lessons = Lesson::find()
            ->location($locationId)
            ->andWhere(['lesson.id' => $lessonOwingIds]);
        $dataProvider = new ActiveDataProvider([
            'query' => $lessons,
            'pagination' => false,
        ]);
        return $this->render('newindex', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $searchModel = new LessonSearch();
        $request = Yii::$app->request;
        $dataProvider = $searchModel->search($request->queryParams);
        $dataProvider->pagination->pageSize = 200;
        $lessonQuery = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->activePrivateLessons()
            ->joinWith(['privateLesson'])
            ->notCanceled();
        $firstLesson = $lessonQuery->orderBy(['lesson.date' => SORT_ASC])->one();
        $lastLesson = $lessonQuery->orderBy(['lesson.date' => SORT_DESC])->one();
        $firstLessonDate = Carbon::parse($firstLesson->date)->format('M d, Y');
        $lastLessonDate = Carbon::parse($lastLesson->date)->format('M d, Y');
        return $this->render('index-new', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'firstLessonDate' => $firstLessonDate,
            'lastLessonDate' => $lastLessonDate
        ]);
    }

    /**
     * Displays a single Lesson model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = $this->findModel($id);
        $enrolment = Enrolment::findOne(['courseId' => $model->courseId]);
        $notes = Note::find()
            ->andWhere(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON])
            ->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

        $groupLessonStudents = Student::find()
            ->notDeleted()
            ->joinWith(['enrolments' => function ($query) use ($id) {
                $query->joinWith(['course' => function ($query) use ($id) {
                    $query->joinWith(['program' => function ($query) use ($id) {
                        $query->group();
                    }]);
                    $query->joinWith(['lessons' => function ($query) use ($id) {
                        $query->andWhere(['lesson.id' => $id]);
                    }])
                        ->confirmed()
                        ->notDeleted();
                }])
                    ->notDeleted()
                    ->isConfirmed();
            }])
            ->location($locationId);

        $studentDataProvider = new ActiveDataProvider([
            'query' => $groupLessonStudents,
        ]);
        $payments = LessonPayment::find()
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
            }])
            ->andWhere(['lesson_payment.lessonId' => $id])
            ->notDeleted();
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments
        ]);
        $logDataProvider = new ActiveDataProvider([
            'query' => LogHistory::find()->lesson($id)
        ]);

        return $this->render('view', [
            'model' => $model,
            'noteDataProvider' => $noteDataProvider,
            'studentDataProvider' => $studentDataProvider,
            'paymentsDataProvider' => $paymentsDataProvider,
            'logDataProvider' => $logDataProvider,
        ]);
    }

    public function actionValidateOnUpdate($id, $teacherId = null)
    {
        $errors = [];
        $model = $this->findModel($id);
        $model->programId = $model->course->programId;
        if (empty($teacherId)) {
            $model->setScenario(Lesson::SCENARIO_EDIT);
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($model->date)) {
                $errors = ActiveForm::validate($model);
                return $errors;
            } else {
                return $errors;
            }
        }
    }

    public function actionFetchDuration($id)
    {
        $model = $this->findModel($id);
        return $model->duration;
    }
    /**
     * Updates an existing Lesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionEditClassroom($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->setScenario(Lesson::SCENARIO_EDIT_CLASSROOM);
        $data = $this->renderAjax('classroom/_form', [
            'model' => $model,
        ]);
        if ($request->post()) {
            if ($model->load($request->post()) && $model->save()) {
                Lesson::triggerPusher();
                return [
                    'status' => true
                ];
            } else {
                return [
                    'status' => false,
                    'errors' => ActiveForm::validate($model),
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldDate = $model->date;
        $model->date = Yii::$app->formatter->asDateTime($model->date);
        $user = User::findOne(['id' => Yii::$app->user->id]);
        $model->userName = $user->publicIdentity;
        $model->on(
            Lesson::EVENT_RESCHEDULE_ATTEMPTED,
            [new LessonReschedule(), 'reschedule'],
            ['oldAttrtibutes' => $model->getOldAttributes()]
        );
        $data = $this->renderAjax('_form', [
            'model' => $model,
            'privateLessonModel' => $model->privateLesson
        ]);
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->hasExpiryDate()) {
                $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);
                $privateLessonModel->load(Yii::$app->getRequest()->getBodyParams(), 'PrivateLesson');
                $privateLessonModel->expiryDate = (new \DateTime($privateLessonModel->expiryDate))->format('Y-m-d H:i:s');
                $privateLessonModel->save();
            }
            if (empty($model->date)) {
                $model->date =  $oldDate;
                $model->status = Lesson::STATUS_UNSCHEDULED;
                $model->save();
                $response = [
                    'status' => true,
                    'url' => Url::to(['lesson/view', 'id' => $model->id])
                ];
            } else {
                $model->setScenario(Lesson::SCENARIO_EDIT);
                if ($model->validate()) {
                    $model->status = Lesson::STATUS_CANCELED;
                    $duration = new \DateTime($model->duration);
                    $model->duration = $duration->format('H:i:s');
                    $lessonDate = new \DateTime($model->date);
                    $model->date = $lessonDate->format('Y-m-d H:i:s');
                    if ($model->save()) {
                        $emailNotifyTypes = NotificationEmailType::find()->all();
                        if($model->isPrivate()){
                            foreach($emailNotifyTypes as $emailNotifyType) {
                                $emailStatus = new PrivateLessonEmailStatus();
                                $emailStatus->lessonId = $model->id;
                                $emailStatus->notificationType = $emailNotifyType->id;
                                $emailStatus->status = false;
                                $emailStatus->save();
                            }
                        } else {
                            if ($model->enrolment) {
                                $groupStudents = Student::find()
                                        ->notDeleted()
                                        ->groupCourseEnrolled($model->enrolment->course->id)->all();
                                foreach($groupStudents as $student){
                                    foreach($emailNotifyTypes as $emailNotifyType) {
                                        $emailStatus = new GroupLessonEmailStatus();
                                        $emailStatus->lessonId = $model->id;
                                        $emailStatus->studentId = $student->id;
                                        $emailStatus->notificationType = $emailNotifyType->id;
                                        $emailStatus->status = false;
                                        $emailStatus->save();
                                    }
                                }
                            }
                        } 
                        
                        $response = [
                            'status' => true,
                            'url' => Url::to(['lesson/view', 'id' => $model->id])
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'errors' => current(ActiveForm::validate($model))
                    ];
                }
            }
            Lesson::triggerPusher();
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    /**
     * Finds the Lesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Lesson the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Lesson::find()->location($locationId)
            ->notDeleted()
            ->andWhere(['lesson.id' => $id])->one();
        if ($model !== null) {
            if ($model->leaf) {
                if (!$model->leaf->isCanceled()) {
                    $this->redirect(['lesson/view', 'id' => $model->leaf->id]);
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            } else if ($model->isCanceled()) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function fetchConflictedLesson($course)
    {
        $model = new LessonReview();
        $lessons = Lesson::find()
            ->notDeleted()
            ->andWhere(['courseId' => $course->id])
            ->notConfirmed()
            ->notCanceled()
            ->all();
        $conflictedLessons = $model->getConflicts($lessons);
        $lessons = [];
        if (!empty($conflictedLessons['lessonIds'])) {
            $lessons = Lesson::find()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->andWhere(['IN', 'lesson.id', $conflictedLessons['lessonIds']])
                ->all();
        }
        return $lessons;
    }

    public function resolveSingleLesson($lesson, $oldDate)
    {
        if ($lesson->date) {
            $lesson->setScenario(Lesson::SCENARIO_EDIT_REVIEW_LESSON);
            $lesson->date = (new \DateTime($lesson->date))->format('Y-m-d H:i:s');
        } else {
            $lesson->date = $oldDate;
            $lesson->status = Lesson::STATUS_UNSCHEDULED;
        }
        if ($lesson->save()) {
            $response = [
                'status' => true
            ];
        } else {
            $response = [
                'status' => false,
                'errors' => ActiveForm::validate($lesson),
            ];
        }
        return $response;
    }

    public function resolveAllLesson($conflictedLessons, $lesson)
    {
        foreach ($conflictedLessons as $conflictedLesson) {
            $conflictedLesson->duration = $lesson->duration;
            if (!empty($lesson->date)) {
                $day = (new \DateTime($lesson->date))->format('N');
                $lessonDay = (new \DateTime($conflictedLesson->date))->format('N');
                $time = (new \DateTime($lesson->date))->format('H:i:s');
                list($hour, $minute, $second) = explode(':', $time);
                $dayList = Course::getWeekdaysList();
                $dayName = $dayList[$day];
                if ($day === $lessonDay) {
                    $lessonDate = new \DateTime($conflictedLesson->date);
                    $lessonDate->setTime($hour, $minute, $second);
                    $conflictedLesson->date = $lessonDate->format('Y-m-d H:i:s');
                } else {
                    $lessonDate = new \DateTime($conflictedLesson->date);
                    $lessonDate->modify('next ' . $dayName);
                    $lessonDate->setTime($hour, $minute, $second);
                    $conflictedLesson->date = $lessonDate->format('Y-m-d H:i:s');
                }
            } else {
                $conflictedLesson->status = Lesson::STATUS_UNSCHEDULED;
            }
            if (!$conflictedLesson->save()) {
                Yii::error('Resolve lesson conflict: ' . \yii\helpers\VarDumper::dumpAsString($conflictedLesson->getErrors()));
            }
        }
        $response = [
            'status' => true
        ];
        return $response;
    }

    public function actionUpdateField($id)
    {
        $lessonReview = new LessonReview();
        $lessonReview->load(Yii::$app->request->get());
        $model = $this->findModel($id);
        $existingDate = $model->date;
        $data = $this->renderAjax('/lesson/review/_form', [
            'model' => $model,
            'lessonReview' => $lessonReview
        ]);
        $response = [
            'status' => true,
            'data' => $data
        ];
        if ($model->load(Yii::$app->request->post()) && !empty($model->applyContext)) {
            if ($model->isResolveSingleLesson()) {
                $response = $this->resolveSingleLesson($model, $existingDate);
            } else {
                $conflictedLessons = $this->fetchConflictedLesson($model->course);
                $response = $this->resolveAllLesson($conflictedLessons, $model);
            }
        }
        return $response;
    }

    public function actionReview()
    {
        $model = new LessonReview();
        $request = Yii::$app->request;
        $model->load($request->get());
        $newTeacherId = $model->teacherId;
        $oldLessonIds[] = null;
        $locationId = Yii::$app->filecache->get('locationId' . $model->courseId);
        if($locationId == false)
        {
            $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
            Yii::$app->filecache->set('locationId' . $model->courseId, $locationId, 60);
        }
        if ($model->courseId) {
            $courseModel = Course::findOne(['id' => $model->courseId]);
            $startDate = new \DateTime($model->rescheduleBeginDate);
            $lessons = Lesson::find()
                    ->notDeleted()
                    ->notConfirmed()
                    ->statusScheduled()
                    ->andWhere(['courseId' => $model->courseId])
                    ->andWhere(['>=', 'DATE(lesson.date)', $startDate->format('Y-m-d')])
                    ->orderBy(['lesson.date' => SORT_ASC])
                    ->all();
            if ($model->rescheduleBeginDate) {
                $queryData = Lesson::find()
                        ->select(['lesson.id','lesson.type','lesson.date','duration'])
                        ->notDeleted()
                        ->andWhere(['courseId' => $model->courseId])
                        ->andWhere(['>=', 'DATE(lesson.date)', $startDate->format('Y-m-d')])
                        ->orderBy(['lesson.date' => SORT_ASC])
                        ->notCanceled()
                        ->isConfirmed();
                $oldLessons = $queryData->all();
                $oldLessonIds = ArrayHelper::getColumn($oldLessons, function ($element) {
                    return $element->id;
                });
                $oldLessonsRe = $queryData->statusScheduled()->all();
                $oldLessonsReIds = ArrayHelper::getColumn($oldLessonsRe, function ($element) {
                    return $element->id;
                });
                foreach ($lessons as $i => $lesson) {
                    $lesson->lessonId = $oldLessonsReIds;
                }
            }
        } else if ($model->enrolmentIds) {
            $changesFrom = (new \DateTime($model->changesFrom))->format('Y-m-d');
            $lessonQuery =  Lesson::find()
                        ->select(['lesson.id','lesson.type','lesson.date','duration'])
                        ->notDeleted()
                        ->andWhere(['>=', 'DATE(lesson.date)', $changesFrom])
                        ->enrolment($model->enrolmentIds)
                        ->notCanceled()
                        ->orderBy(['lesson.date' => SORT_ASC]);
            $oldLessons = $lessonQuery->isConfirmed()->all();
            $lessons = $lessonQuery->notConfirmed()->all();
            $oldLessonsIds = ArrayHelper::getColumn($oldLessons, 'id');
            foreach ($lessons as $i => $lesson) {
                $lesson->lessonId = $oldLessonsIds;
            }
        }
        if (!$model->courseId) {
            $teacherId = $model->teacherId;
        } else {
            $teacherId = $courseModel->teacherId;
        }
        $model->teacherId = $teacherId;
        $conflictedLessons = $model->getConflicts($lessons);
        $lessonCount = count($lessons);
        $conflictedLessonIdsCount = count($conflictedLessons['lessonIds']);
        $lessonIds = ArrayHelper::getColumn($lessons, function ($element) {
            return $element->id;
        });
       
        $unscheduledLessonCount = Lesson::find()
            ->select(['lesson.id','lesson.type','lesson.date','duration'])
            ->andWhere(['id' => $lessonIds])
            ->andWhere(['NOT', ['id' => $conflictedLessons['holidayConflictedLessonIds']]])
            ->unscheduled()
            ->count();
        
        $query = Lesson::find()
            ->select(['lesson.id','lesson.type','lesson.date','duration'])
            ->andWhere(['id' => $lessonIds])
            ->orderBy(['lesson.date' => SORT_ASC]);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        $unscheduledLesson = Lesson::find()
            ->select(['lesson.type','lesson.date','duration'])
            ->andWhere(['id' => $oldLessonIds])
            ->unscheduled()
            ->orderBy(['lesson.date' => SORT_ASC]);
        $unscheduledLessonDataProvider = new ActiveDataProvider([
            'query' =>  $unscheduledLesson,
            'pagination' => false
        ]);

        $rescheduledLesson = Lesson::find()
            ->select(['lesson.type','lesson.date','duration'])
            ->andWhere(['id' => $oldLessonIds])
            ->rescheduled()
            ->orderBy(['lesson.date' => SORT_ASC]);

        $rescheduledLessonDataProvider = new ActiveDataProvider([
            'query' => $rescheduledLesson,
            'pagination' => false
        ]);
        if ($model->courseModelId) {
            $courseModel = Course::findOne($model->courseModelId);
        }
        Yii::$app->filecache->flush();
        return $this->render('review', [
            'courseModel' => $courseModel ?? null,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflictedLessons['conflicts'],
            'model' => $model,
            'holidayConflictedLessonIds' => $conflictedLessons['holidayConflictedLessonIds'],
            'lessonCount' => $lessonCount,
            'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
            'unscheduledLessonCount' => $unscheduledLessonCount,
            'unscheduledLessonDataProvider' => $unscheduledLessonDataProvider,
            'rescheduledLessonDataProvider' => $rescheduledLessonDataProvider,
            'newTeacherId' => $model->isBulkTeacherChange ? $newTeacherId : null,
        ]);
    }

    public function actionFetchConflict()
    {
        $model = new LessonReview();
        $request = Yii::$app->request;
        $model->load($request->get());
        if ($model->courseId) {
            $courseModel = Course::findOne(['id' => $model->courseId]);
            $draftLessons = Lesson::find()
                ->notDeleted()
                ->andWhere(['courseId' => $courseModel->id])
                ->notConfirmed()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->all();
            if ($model->rescheduleBeginDate) {
                $startDate = new \DateTime($model->rescheduleBeginDate);
                $oldLessonsRe = Lesson::find()
                    ->andWhere(['courseId' => $courseModel->id])
                    ->notDeleted()
                    ->isConfirmed()
                    ->statusScheduled()
                    ->andWhere(['>=', 'DATE(lesson.date)', $startDate->format('Y-m-d')])
                    ->orderBy(['lesson.date' => SORT_ASC])
                    ->all();
                $oldLessonsReIds = ArrayHelper::getColumn($oldLessonsRe, function ($element) {
                    return $element->id;
                });
                foreach ($draftLessons as $i => $lesson) {
                    $lesson->lessonId = $oldLessonsReIds;
                }
            }
        } else if ($model->enrolmentIds) {
            $changesFrom = (new \DateTime($model->changesFrom))->format('Y-m-d');
            $oldLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->andWhere(['>=', 'DATE(lesson.date)', $changesFrom])
                ->enrolment($model->enrolmentIds)
                ->notCanceled()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->all();
            $draftLessons = Lesson::find()
                ->notDeleted()
                ->andWhere(['>=', 'DATE(lesson.date)', $changesFrom])
                ->enrolment($model->enrolmentIds)
                ->notCanceled()
                ->notConfirmed()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->all();
            $oldLessonsReIds = ArrayHelper::getColumn($oldLessons, function ($element) {
                return $element->id;
            });
            foreach ($draftLessons as $i => $lesson) {
                $lesson->lessonId = $oldLessonsReIds;
            }
        }
        $conflictedLessons = $model->getConflicts($draftLessons);

        return [
            'hasConflict' => !empty($conflictedLessons['lessonIds'])
        ];
    }

    public function actionConfirm()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $model = new LessonConfirm();
        $request = Yii::$app->request;
        $model->load($request->get());
        $courseModel = Course::findOne(['id' => $model->courseId]);
        if ($courseModel) {
            $courseModel->updateAttributes([
                'isConfirmed' => true
            ]);
        }
        if ($model->enrolmentIds) {
            $changesFrom = (new \DateTime($model->changesFrom))->format('Y-m-d');
            $lessons = Lesson::find()
                ->notDeleted()
                ->notConfirmed()
                ->andWhere(['>=', 'DATE(lesson.date)', $changesFrom])
                ->enrolment($model->enrolmentIds)
                ->orderBy(['lesson.date' => SORT_ASC])
                ->notCanceled()
                ->all();

        } else {
            $lessons = Lesson::find()
                ->notDeleted()
                ->andWhere(['courseId' => $courseModel->id])
                ->notConfirmed()
                ->orderBy(['lesson.date' => SORT_ASC])
                ->groupBy('lesson.id')
                ->limit(12)
                ->all();
        }
        $lesson = end($lessons);

        if ($model->rescheduleBeginDate && $model->rescheduleEndDate) {
            $model->confirmBulkReschedule();
        }
        if ($model->enrolmentIds) {
            foreach ($model->enrolmentIds as $enrolmentId) {
                if ($enrolmentId) {
                    $model->confirmEnrolmentTeacherChange($enrolmentId);
                }
            }
        }
        if (!$model->rescheduleBeginDate) {
            foreach ($lessons as $lesson) {
                $lesson->makeAsRoot();
            }
            if (!$model->enrolmentIds) {
                Yii::$app->queue->push(new MakeLessonAsRoot([
                    'userId' => Yii::$app->user->id,
                    'courseId' => $courseModel->id
                ]));
            }
        }   
        $emailNotifyTypes = NotificationEmailType::find()->all();
        foreach ($lessons as $lesson) {
            $lesson->isConfirmed = true;
            $lesson->save();
            if (!$model->rescheduleBeginDate) {
                $lesson->setDiscount();
            }
                if ($lesson->isPrivate()) {
                    foreach($emailNotifyTypes as $emailNotifyType) {
                        $emailStatus = new PrivateLessonEmailStatus();
                        $emailStatus->lessonId = $lesson->id;
                        $emailStatus->notificationType = $emailNotifyType->id;
                        $emailStatus->status = false;
                        $emailStatus->save();
                    }
                }
        }
        if (!$model->enrolmentIds) {
            Yii::$app->queue->push(new QueueLessonConfirm([
                'userId' => Yii::$app->user->id,
                'courseId' => $courseModel->id,
                'rescheduleBeginDate' => $model->rescheduleBeginDate ? $model->rescheduleBeginDate : null
            ]));
        }
        if (!$model->rescheduleBeginDate && !$model->changesFrom && $courseModel->isPrivate()) {
            Yii::$app->queue->push(new EnrolmentConfirm([
                'userId' => Yii::$app->user->id,
                'enrolmentId' => $courseModel->enrolment->id
            ]));
        }
        if (!$model->courseId) {
            $message = "Future lesson's teacher have been changed successfully";
            foreach ($model->enrolmentIds as $enrolmentId) {
                $enrolment = Enrolment::findOne($enrolmentId);
                $enrolment->course->updateDates();
            }
            $link = $this->redirect(['enrolment/index']);
        } else if ($courseModel->program->isPrivate()) {
            if ($model->rescheduleBeginDate) {
                // $message = 'Future lessons have been changed successfully';
                $courseModel->enrolment->updateAttributes(['isEnableRescheduleInfo' => true]);
                return $this->redirect(['enrolment/view', 'id' => $courseModel->enrolment->id]);
            } else {
                $model->confirmCustomer();
                $courseModel->enrolment->updateAttributes(['isEnableInfo' => true]);
                return $this->redirect(['/enrolment/view', 'id' => $courseModel->enrolment->id]);
            }
        } else {
            $message = 'Course has been created successfully';
            $link = $this->redirect(['course/view', 'id' => $model->courseId]);
        }
        Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => $message
        ]);
        Yii::$app->session->remove('locationId');
        return $link;
    }

    public function actionInvoice($id)
    {
        $model = Lesson::findOne(['id' => $id]);

        if ($model->canInvoice()) {
            if ($model->hasInvoice()) {
                $invoice = $model->invoice;
            } else {
                $invoice = $model->createPrivateLessonInvoice();
            }

            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Invoice can be generated against completed scheduled lessons only.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
    }

    public function actionModifyClassroom($id, $classroomId)
    {
        $model = Lesson::findOne($id);
        $model->setScenario(Lesson::SCENARIO_EDIT_CLASSROOM);
        $model->classroomId = $classroomId;
        if ($model->validate()) {
            $model->save(false);
            Lesson::triggerPusher();
            $response = [
                'status' => true,
            ];
        } else {
            $model = ActiveForm::validate($model);
            $response = [
                'status' => false,
                'errors' => current($model),
            ];
        }

        return $response;
    }

    public function actionPayment($lessonId, $enrolmentId)
    {
        $payments = LessonPayment::find()
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
            }])
            ->andWhere(['lesson_payment.lessonId' => $lessonId, 'lesson_payment.enrolmentId' => $enrolmentId])
            ->notDeleted();

        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments,
        ]);
        $data = $this->renderAjax('payment/view', [
            'paymentsDataProvider' => $paymentsDataProvider
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function actionSubstitute($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            $model->save();
            Lesson::triggerPusher();
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => false,
                'errors' => ActiveForm::validate($model)
            ];
        }
    }

    public function actionUnschedule($id)
    {
        $model = $this->findModel($id);
        $post  = Yii::$app->request->post();
        $unscheduleLessonModel = new UnscheduleLesson();
        $unscheduleLessonModel->load($post);
        if ($model->unschedule($unscheduleLessonModel->reason)) {
            Lesson::triggerPusher();
            return [
                'status' => true,
                'message' => 'Lesson unscheduled successfully',
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Lesson cannot be unscheduled',
            ];
        }
    }

    public function actionEditDueDate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        $data = $this->renderAjax('_edit-due-date', [
            'model' => $model,
        ]);
        if ($request->post()) {
            if ($model->load($request->post())) {
                $model->dueDate = (new \DateTime($model->dueDate))->format('Y-m-d');
                $model->save();
                return [
                    'status' => true
                ];
            } else {
                return [
                    'status' => false,
                    'errors' => ActiveForm::validate($model),
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }

    public function actionCreditTransfer($id)
    {
        $model = $this->findModel($id);
        if ($model->hasInvoice()) {
            $model->creditTransfer($model->invoice);
            return [
                'status' => true,
                'message' => 'Lesson credits successfully transferd to invoice',
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Lesson not yet invoiced',
            ];
        }
    }

    public function actionEditPrice($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isPost) {
            if ($model->load($request->post())) {
                if ($model->save()) {
                    $response = [
                        'status' => true
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'errors' => ActiveForm::validate($model)
                    ];
                }
            }
        } else {
            $data = $this->renderAjax('_price-form', [
                'model' => $model
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionEditTax($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->setScenario(Lesson::SCENARIO_EDIT);
        if ($request->isPost) {
            if ($model->load($request->post())) {
                $model->save();
                $response = [
                    'status' => true
                ];
            }
        } else {
            $data = $this->renderAjax('_tax-form', [
                'model' => $model
            ]);
            $response = [
                'status' => empty(current(ActiveForm::validate($model))),
                'data' => $data,
                'errors' => current(ActiveForm::validate($model))
            ];
        }
        return $response;
    }

    public function actionEditCost($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isPost) {
            if ($model->load($request->post())) {
                if ($model->save()) {
                    $response = [
                        'status' => true
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'errors' => ActiveForm::validate($model)
                    ];
                }
            }
        } else {
            $data = $this->renderAjax('/lesson/cost/_form', [
                'model' => $model
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
}
