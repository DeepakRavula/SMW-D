<?php

namespace backend\controllers;

use Yii;
use common\models\Course;
use common\models\log\CourseLog;
use common\models\Lesson;
use common\models\ExtraLesson;
use common\models\log\LessonLog;
use common\models\Location;
use common\models\Qualification;
use backend\models\search\CourseSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\UserProfile;
use common\models\UserPhone;
use common\models\UserEmail;
use common\models\UserAddress;
use yii\helpers\Url;
use common\models\Student;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use backend\models\UserForm;
use yii\base\Model;
use common\models\CourseExtra;
use common\models\CourseSchedule;
use backend\models\EnrolmentForm;
use yii\web\Response;
use common\models\TeacherAvailability;
use common\models\Enrolment;
use common\models\log\LogHistory;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use common\models\Payment;
use common\models\CustomerReferralSource;

/**
 * CourseController implements the CRUD actions for Course model.
 */
class CourseController extends BaseController
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
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['fetch-teacher-availability', 'fetch-lessons', 
                    'fetch-group', 'change', 'teachers', 'create-enrolment-basic',
                    'create-enrolment-detail', 'create-enrolment-date-detail'
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'fetch-teacher-availability',
                            'course-date', 'create', 'update', 'delete', 'teachers',
                            'fetch-group', 'change', 'create-enrolment-basic',
                            'create-enrolment-detail', 'create-enrolment-date-detail'
                        ],
                        'roles' => ['manageGroupLessons'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Course models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Course model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $extraCourse = CourseExtra::find()
                ->andWhere(['courseId' => $id])
                ->all();
        $courseId = ArrayHelper::map($extraCourse, 'extraCourseId', 'extraCourseId');
        $courseId[] = $id;
        $studentDataProvider = new ActiveDataProvider([
            'query' => Student::find()
                ->notDeleted()
                ->groupCourseEnrolled($id)
                ->active(),
        ]);

        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $courseId])
                ->notCanceled()
                ->isConfirmed()
                ->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
        ]);
        $logDataProvider    = new ActiveDataProvider([
            'query' => LogHistory::find()
                ->course($id)]);
        return $this->render(
            'view',
                [
                'model' => $this->findModel($id),
                'courseId' => $id,
                'studentDataProvider' => $studentDataProvider,
                'lessonDataProvider' => $lessonDataProvider,
                'logDataProvider' => $logDataProvider,
        ]
        );
    }

    public function actionFetchTeacherAvailability($teacherId)
    {
        $query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->andWhere(['user_id' => $teacherId]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $data = $this->renderAjax('_teacher-availability', [
            'teacherDataProvider' => $teacherDataProvider,
        ]);
        return $data;
    }
    /**
     * Creates a new Course model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function getCourseDate($courseScheduleModels)
    {
        $courseDates = ArrayHelper::getColumn($courseScheduleModels, function ($courseSchedule) {
            return $courseSchedule['fromTime'];
        });
        usort($courseDates, function ($a, $b) {
            $date1 = new \DateTime($a);
            $date2 = new \DateTime($b);
            return $date1 < $date2 ? -1: 1;
        });
        return $courseDates[0];
    }
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $model = new Course();
        $courseSchedule = [new CourseSchedule()];
        $model->setScenario(Course::SCENARIO_GROUP_COURSE);
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Course::EVENT_AFTER_INSERT, [new CourseLog(), 'create'], ['loggedUser' => $loggedUser]);
        if ($model->load($request->post())) {
            $courseScheduleModels = UserForm::createMultiple(CourseSchedule::classname());
            Model::loadMultiple($courseScheduleModels, $request->post());
            if ($request->isAjax) {
                $response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validate($model),
                        ActiveForm::validateMultiple($courseScheduleModels)
                );
            }
            $valid = $model->validate();
            $valid = (Model::validateMultiple($courseScheduleModels)) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $model->startDate           = $this->getCourseDate($courseScheduleModels);
                    $model->lessonsPerWeekCount = count($courseScheduleModels);
                    $model->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
                    if ($flag = $model->save(false)) {
                        foreach ($courseScheduleModels as $courseScheduleModel) {
                            $courseScheduleModel->courseId = $model->id;
                            $courseScheduleModel->duration = $model->duration;
                            $dayList                       = Course::getWeekdaysList();
                            $courseScheduleModel->day      = array_search(
                                $courseScheduleModel->day,
                                $dayList
                            );
                            if (!($flag                          = $courseScheduleModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $model->createLessons();
                        $model->trigger(Course::EVENT_CREATE);
                        return $this->redirect(['lesson/review', 'LessonReview[courseId]' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'courseSchedule' => (empty($courseSchedule)) ? [new CourseSchedule] : $courseSchedule
            ]);
        }
    }
    /**
     * Updates an existing Course model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $teacherModel = ArrayHelper::map(
            User::find()
                ->excludeWalkin()
                ->joinWith('userLocation ul')
                ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                ->andWhere(['raa.item_name' => 'teacher'])
                ->andWhere(['ul.location_id' => Location::findOne(['slug' => \Yii::$app->location])->id])
                ->notDeleted()
                ->all(),
                'id', 'userProfile.fullName'
            );
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'teacher' => $teacherModel,
            ]);
        }
    }

    /**
     * Deletes an existing Course model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Course model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Course the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Course::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTeachers()
    {
        $location_id = Location::findOne(['slug' => \Yii::$app->location])->id;
        $programId = !empty($_POST['depdrop_parents'][0]) ? $_POST['depdrop_parents'][0] : null;
        $program = Yii::$app->request->post('program');
        $query = Qualification::find()
            ->joinWith(['teacher' => function ($query) use ($location_id) {
                $query->joinWith(['userLocation' => function ($query) use ($location_id) {
                    $query->join('LEFT JOIN', 'user_profile', 'user_profile.user_id = user_location.user_id')
                        ->joinWith('teacherAvailability')
                        ->andWhere(['location_id' => $location_id]);
                }]);
            }])
            ->notDeleted()
            ->groupBy('user_profile.user_id')
            ->orderBy(['user_profile.firstname' => SORT_ASC]);
        if ($programId) {
            $query->andWhere(['program_id' => $programId]);
            $value = 'name';
        } else {
            $value = 'text';
            if ($program) {
                $query->andWhere(['program_id' => $program]);
            }
        }
        $qualifications = $query->all();
        $output = [];
        foreach ($qualifications as  $i => $qualification) {
            if ($i == 0 && !$programId) {
                $output[] = [
                    'id' => '',
                    $value => ''
                ];
            }
            $output[] = [
                'id' => $qualification->teacher->id,
                $value => $qualification->teacher->publicIdentity
            ];
        }
        $result = [
            'output' => $output,
            'selected' => ''
        ];

        return $result;
    }
    public function actionFetchGroup($studentId, $courseName = null)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $groupEnrolments = Enrolment::find()
            ->select(['courseId'])
            ->joinWith(['course' => function ($query) use ($locationId) {
                $query->groupProgram($locationId)
                        ->confirmed();
            }])
            ->andWhere(['enrolment.studentId' => $studentId])
            ->isConfirmed();
        $groupCourses = Course::find()
            ->regular()
            ->joinWith(['program' => function ($query) {
                $query->group();
            }])
            ->andWhere(['NOT IN', 'course.id', $groupEnrolments])
            ->andWhere(['locationId' => $locationId])
            ->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
            ->confirmed();
        if (!empty($courseName)) {
            $groupCourses->andWhere(['LIKE', 'program.name', $courseName]);
        }
        $groupDataProvider = new ActiveDataProvider([
            'query' => $groupCourses,
        ]);

        $data = $this->renderAjax('/student/enrolment/_form-group', [
            'groupDataProvider' => $groupDataProvider,
            'student' => Student::findOne(['id' => $studentId])
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }
        
    public function actionChange()
    {
        $lessonSearchRequest = Yii::$app->request->get('LessonSearch');
        $lessonIds = $lessonSearchRequest['ids'];
        $lessons = Lesson::findAll($lessonIds);
        $model = Course::findOne(end($lessons)->courseId);
        $model->studentId = $model->enrolment->studentId;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                foreach ($lessons as $lesson) {
                    $enrolmentId = $lesson->enrolment->id;
                    $newLesson = new ExtraLesson();
                    $newLesson->programId = $model->programId;
                    $newLesson->duration = $lesson->duration;
                    $newLesson->date = (new \DateTime($lesson->date))->format('Y-m-d g:i A');
                    $newLesson->teacherId = $model->teacherId;
                    $newLesson->studentId = $model->studentId;
                    $newLesson->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
                    $newLesson->setScenario(Lesson::SCENARIO_CREATE);
                    $newLesson->addPrivate(Lesson::STATUS_UNSCHEDULED);
                    $hasCreditInvoice = false;
                    $payment = new Payment();
                    if ($newLesson->save()) {
                        $newLesson->makeAsRoot();
                        $invoice = $newLesson->takePayment();
                        if ($lesson->hasLessonCredit($enrolmentId)) {
                            if ($invoice->balance < $lesson->getLessonCreditAmount($enrolmentId)) {
                                $amount = $invoice->balance;
                                if (!$hasCreditInvoice) {
                                    $creditInvoice = $lesson->addLessonCreditInvoice();
                                    $creditInvoice->save();
                                }
                                $payment->amount = $lesson->getLessonCreditAmount($enrolmentId) - $amount;
                                $creditInvoice->addPayment($lesson, $payment);
                                $hasCreditInvoice = true;
                            } else {
                                $amount = $lesson->getLessonCreditAmount($enrolmentId);
                            }
                            $payment->amount = $amount;
                            $invoice->addPayment($lesson, $payment);
                        }
                        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                        $newLesson->on(
                            Lesson::EVENT_AFTER_INSERT,
                            [new LessonLog(), 'extraLessonCreate'],
                            ['loggedUser' => $loggedUser]
                        );
                        $newLesson->trigger(Lesson::EVENT_AFTER_INSERT);
                        $lesson->cancel();
                    }
                }
                $response = [
                    'status' => true,
                    'message' => 'Lessons successfuly changed'
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        } else {
            $data = $this->renderAjax('_change-form', [
                'model' => $model,
                'lessonIds' => $lessonIds
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionCreateEnrolmentBasic($studentId = null, $isReverse)
    {
        $courseDetailData = Yii::$app->request->get('EnrolmentForm');
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $courseDetail = new EnrolmentForm(['scenario' => EnrolmentForm::SCENARIO_BASIC]);
        if ($courseDetailData) {
            $courseDetail->load(Yii::$app->request->get());
        }
        if ($studentId) {
            $student = Student::findOne($studentId);
            $customerDiscount = $student->customer->hasDiscount() ? $student->customer->customerDiscount : null;
        } else {
            $student = null;
            $customerDiscount = null;
        }
        
        if (Yii::$app->request->isPost) {
            if ($courseDetail->load(Yii::$app->request->post()) && $courseDetail->validate()) {
                $courseDetail->setScenario(EnrolmentForm::SCENARIO_DATE_DETAILED);
                $courseData = $this->renderAjax('enrolment/_course-start-date', [
                    'model' => $courseDetail,
                    'student' => $student,
                    'isReverse' => $isReverse
                ]);
                $response = [
                    'status' => true,
                    'data' => $courseData,
                    'customerDiscount' => $customerDiscount
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseDetail)
                ];
            }
        } else {
            $data = $this->renderAjax('enrolment/_course-basic', [
                'model' => $courseDetail,
                'student' => $student,
                'isReverse' => $isReverse,
                'customerDiscount' => $customerDiscount
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionCreateEnrolmentDetail($studentId = null, $isReverse)
    {
        $courseDetailData = Yii::$app->request->get('EnrolmentForm');
        $courseDetail = new EnrolmentForm();
        if ($courseDetailData) {
            $courseDetail->load(Yii::$app->request->get());
        }
        $courseDetail->setScenario(EnrolmentForm::SCENARIO_DETAILED);
        if ($studentId) {
            $student = Student::findOne($studentId);
            $customerDiscount = $student->customer->hasDiscount() ? $student->customer->customerDiscount : null;
        } else {
            $student = null;
            $customerDiscount = null;
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $teachers = User::find()
                    ->teachers([$courseDetail->programId], $locationId)
                    ->join('LEFT JOIN', 'user_profile', 'user_profile.user_id = ul.user_id')
                    ->notDeleted()
                    ->orderBy(['user_profile.firstname' => SORT_ASC])
                    ->all();
        if (Yii::$app->request->isPost) {
            if ($courseDetail->load(Yii::$app->request->post()) && $courseDetail->validate()) {
                if ($isReverse) {
                    $courseDetail->setScenario(EnrolmentForm::SCENARIO_CUSTOMER);
                    $courseData = $this->renderAjax('/enrolment/new/_form-customer', [
                        'student' => $student,
                        'isReverse' => $isReverse,
                        'courseDetail' => $courseDetail,
                    ]);
                } else {
                    $courseData = null;
                }
                $response = [
                    'status' => true,
                    'data' => $courseData,
                    'url' => !$isReverse ? Url::to(['student/create-enrolment', 'id' => $student->id,
                        'EnrolmentForm' => $courseDetail]) : null
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseDetail)
                ];
            }
        } else {
            $data = $this->renderAjax('enrolment/_course-detail', [
                'model' => $courseDetail,
                'student' => $student,
                'isReverse' => $isReverse,
                'teachers' => $teachers,
                'customerDiscount' => $customerDiscount
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionCreateEnrolmentDateDetail($studentId = null, $isReverse)
    {
        $courseDetailData = Yii::$app->request->get('EnrolmentForm');
        $courseDetail = new EnrolmentForm();
        if ($courseDetailData) {
            $courseDetail->load(Yii::$app->request->get());
        }
        $courseDetail->setScenario(EnrolmentForm::SCENARIO_DATE_DETAILED);
        if ($studentId) {
            $student = Student::findOne($studentId);
            $customerDiscount = $student->customer->hasDiscount() ? $student->customer->customerDiscount : null;
        } else {
            $student = null;
            $customerDiscount = null;
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        if (Yii::$app->request->isPost) {
            if ($courseDetail->load(Yii::$app->request->post()) && $courseDetail->validate()) {
                $courseDetail->setScenario(EnrolmentForm::SCENARIO_DETAILED);
                $teachers = User::find()
                    ->teachers([$courseDetail->programId], $locationId)
                    ->join('LEFT JOIN', 'user_profile', 'user_profile.user_id = ul.user_id')
                    ->notDeleted()
                    ->orderBy(['user_profile.firstname' => SORT_ASC])
                    ->all();
                $data = $this->renderAjax('enrolment/_course-detail', [
                    'model' => $courseDetail,
                    'student' => $student,
                    'isReverse' => $isReverse,
                    'teachers' => $teachers,
                    'customerDiscount' => $customerDiscount
                ]);
                $response = [
                    'status' => true,
                    'data' => $data
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($courseDetail)
                ];
            }
        } else {
            $courseData = $this->renderAjax('enrolment/_course-start-date', [
                'model' => $courseDetail,
                'student' => $student,
                'isReverse' => $isReverse
            ]);
            $response = [
                'status' => true,
                'data' => $courseData,
                'customerDiscount' => $customerDiscount
            ];
        }
        return $response;
    }
}
