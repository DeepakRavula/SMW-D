<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\PaymentCycle;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Course;
use yii\data\ActiveDataProvider;
use backend\models\search\EnrolmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Student;
use common\models\PhoneNumber;
use common\models\Address;
use common\models\UserProfile;
use yii\filters\ContentNegotiator;
/**
 * EnrolmentController implements the CRUD actions for Enrolment model.
 */
class EnrolmentController extends Controller
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
				'only' => ['preview', 'delete', 'edit'],
				'formatParam' => '_format',
				'formats' => [
				   'application/json' => Response::FORMAT_JSON,
				],
			],	 
        ];
    }

    /**
     * Lists all Enrolment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EnrolmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Enrolment model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
				->andWhere([
					'courseId' => $model->course->id,
					'status' => Lesson::STATUS_SCHEDULED
				])
				->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
            'pagination' => false,
        ]);
        
        $paymentCycleDataProvider = new ActiveDataProvider([
            'query' => PaymentCycle::find()
				->andWhere([
					'enrolmentId' => $id,
				]),
            'pagination' => false,
        ]);
		
        return $this->render('view', [
            'model' => $model,
            'lessonDataProvider' => $lessonDataProvider,
            'paymentCycleDataProvider' => $paymentCycleDataProvider,
        ]);
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $oldPaymentFrequency = $model->paymentFrequencyId;
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->hasEditable && $model->save()) {
            if ((int) $oldPaymentFrequency !== (int) $model->paymentFrequencyId) {
                $model->resetPaymentCycle();
            }
            return ['output' => $model->getPaymentFrequency(), 'message' => ''];
        }
    }
    /**
     * Creates a new Enrolment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Enrolment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

	public function actionStudent()
    {
        $model = new Student();

		return $this->render('_form-student', [
			'model' => $model,
		]);
    }

	public function actionCustomer()
    {
        $model = new User();
		$phoneModel = new PhoneNumber();
		$addressModel = new Address();
		$userProfile = new UserProfile();

		return $this->render('_form-customer', [
			'model' => $model,
			'phoneModel' => $phoneModel,
			'addressModel' => $addressModel, 
			'userProfile' => $userProfile
		]);
    }

	public function actionPreview($id)
	{
		$model = $this->findModel($id);
		$data =  $this->renderAjax('/student/enrolment-preview', [
            'model' => $model,
        ]);	
		return [
			'status' => true,
			'data' => $data
		];
	}
    /**
     * Updates an existing Enrolment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $timebits = explode(':', $model->course->fromTime);
		$courseEndDate = (new \DateTime($model->course->endDate))->format('Y-m-d');
		$courseEndDate = new \DateTime($courseEndDate);
        $courseEndDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
		if ($model->course->load(Yii::$app->request->post())) {
			$model->course->setScenario(Course::SCENARIO_EDIT_ENROLMENT);
			$validate = $model->course->validate();
			$errors = $model->course->getErrors();
			if(!empty($errors)) {
				Yii::$app->session->setFlash('alert',
					[
					'options' => ['class' => 'alert-danger'],
					'body' => current(reset($errors)),
				]);
				return $this->redirect(['update', 'id' => $model->id]);
			}
			$existingEndDate = (new \DateTime($model->course->getOldAttribute('endDate')))->format('d-m-Y');
			$endDate = new \DateTime($model->course->endDate);
			if(new \DateTime($existingEndDate) != $endDate) {
				if(new \DateTime($existingEndDate) < $endDate) {
					$interval = new \DateInterval('P1D');
					$period = new \DatePeriod($courseEndDate, $interval, $endDate);

					foreach ($period as $day) {
						$professionalDevelopmentDay = clone $day;
						$professionalDevelopmentDay->modify('last day of previous month');
						$professionalDevelopmentDay->modify('fifth '.$day->format('l'));
						if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
							continue;
						}
						if ((int) $day->format('N') === (int) $model->course->day) {
							$lesson = new Lesson();
							$lesson->setAttributes([
								'courseId' => $model->course->id,
								'teacherId' => $model->course->teacherId,
								'status' => Lesson::STATUS_DRAFTED,
								'date' => $day->format('Y-m-d H:i:s'),
								'duration' => $model->course->duration,
								'isDeleted' => false,
							]);
							$lesson->save();
						}
					}
					return $this->redirect(['/lesson/review', 'courseId' => $model->course->id, 'Enrolment[endDate]' => $model->course->endDate, 'Enrolment[type]' => Enrolment::EDIT_RENEWAL]);
				} else {
					return $this->redirect(['/lesson/review', 'courseId' => $model->course->id, 'Enrolment[endDate]' => $model->course->endDate, 'Enrolment[type]' => Enrolment::EDIT_LEAVE]);
				}
			}
			$rescheduleBeginDate = \DateTime::createFromFormat('d-m-Y', $model->course->rescheduleBeginDate);
			$rescheduleBeginDate = $rescheduleBeginDate->format('Y-m-d 00:00:00');
			Lesson::deleteAll([
				'courseId' => $model->course->id,
				'status' => Lesson::STATUS_DRAFTED,
			]);
			$lessons		 = Lesson::find()
				->where([
					'courseId' => $model->course->id,
					'status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_UNSCHEDULED],
				])
				->andWhere(['>=', 'date', $rescheduleBeginDate])
				->all();
			//lesson start date
			$changedFromTime = (new \DateTime($model->course->fromTime))->format('H:i:s');
			$duration		 = explode(':', $changedFromTime);
			$startDate		 = new \DateTime($model->course->rescheduleBeginDate);
			$dayList = Course::getWeekdaysList();
			$courseDay = $dayList[$model->course->day];
			$day = $startDate->format('l');
			if ($day !== $courseDay) {
				$startDate		 = new \DateTime($model->course->rescheduleBeginDate);
				$startDate->modify('next '.$courseDay);
			}
			foreach ($lessons as $lesson) {
				$professionalDevelopmentDay = clone $startDate;
				$professionalDevelopmentDay->modify('last day of previous month');
				$professionalDevelopmentDay->modify('fifth '.$day);
				if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
					$startDate->modify('next '.$day);
				}
				$originalLessonId	 = $lesson->id;
				$lesson->id			 = null;
				$lesson->isNewRecord = true;
				$lesson->status		 = Lesson::STATUS_DRAFTED;
				$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
				$lesson->date		 = $startDate->format('Y-m-d H:i:s');
				$lesson->save();
				$startDate		 = new \DateTime($lesson->date);
				$day = $startDate->format('l');
				$startDate->modify('next '.$day);
			}
			return $this->redirect(['/lesson/review', 'courseId' => $model->course->id, 'Course[rescheduleBeginDate]' => $model->course->rescheduleBeginDate]);
		} else {
			return $this->render('update', [
					'model' => $model->course,
			]);
		}
    }

    /**
     * Deletes an existing Enrolment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
	    /**
     * Deletes an existing Student model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
  
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
		if ($model->course->program->isPrivate()) {
            $lessons = Lesson::find()
                    ->where(['courseId' => $model->courseId])
                    ->andWhere(['>', 'date', (new \DateTime())->format('Y-m-d H:i:s')])
                    ->all();
            foreach ($lessons as $lesson) {
                $lesson->softDelete();
            }
			$model->softDelete();
        }
       
        return [
			'status' => true,
		];
    }

    /**
     * Finds the Enrolment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Enrolment the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Enrolment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSendMail($id)
    {
		$model      = $this->findModel($id);
		$enrolmentRequest = Yii::$app->request->post('Enrolment');
		if($enrolmentRequest) {
			$model->toEmailAddress = $enrolmentRequest['toEmailAddress'];
			$model->subject = $enrolmentRequest['subject'];
			$model->content = $enrolmentRequest['content'];
			if($model->sendEmail())
			{
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-success'],
					'body' => ' Mail has been sent successfully',
				]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-danger'],
					'body' => 'The customer doesn\'t have email id',
				]);
			}
			return $this->redirect(['view', 'id' => $model->id]);
		}
		
        return $this->redirect(['view', 'id' => $model->id]);
    }
}
