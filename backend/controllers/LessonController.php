<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\PrivateLesson;
use common\models\Enrolment;
use common\models\Program;
use common\models\Course;
use common\models\Invoice;
use common\models\LessonReschedule;
use common\models\ItemType;
use yii\data\ActiveDataProvider;
use backend\models\search\LessonSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Payment;
use common\models\PaymentMethod;
use common\models\CreditUsage;
use yii\web\Response;
use common\models\Vacation;

use yii\widgets\ActiveForm;
/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends Controller
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
        ];
    }

    /**
     * Lists all Lesson models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LessonSearch();
        $searchModel->lessonStatus = Lesson::STATUS_COMPLETED;
        $request = Yii::$app->request;
        $invoiceRequest = $request->get('LessonSearch');
        $searchModel->type = $invoiceRequest['type'];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Lesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($studentId)
    {
		$response = \Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
        $model = new Lesson();
		$model->setScenario(Lesson::SCENARIO_PRIVATE_LESSON);
		$request = Yii::$app->request;
        if ($model->load($request->post())) {
			$studentEnrolment = Enrolment::find()
			   ->joinWith(['course' => function($query) use($model){
				   $query->where(['course.programId' => $model->programId]);
			   }])
				->where(['studentId' => $studentId])
				->one();
            $model->courseId = $studentEnrolment->courseId;
            $model->status = Lesson::STATUS_SCHEDULED;
		    $model->isDeleted = false;
            $lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
            $model->date = $lessonDate->format('Y-m-d H:i:s');
			$model->duration = $studentEnrolment->course->duration;
			if ($model->validate()) {
	            $model->save();
				$response = [
					'status' => true,
				];
			} else {
				$errors = ActiveForm::validate($model);
				$response = [
					'status' => false,
					'errors' =>  $errors
				];
			}
			return $response;
			}
        else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Lesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
        $currentDate = new \DateTime();
        if ($lessonDate < $currentDate) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Completed lessons cannot be editable!.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
        $data = ['model' => $model];
        $view = '_form';
        if ((int) $model->course->program->type === Program::TYPE_PRIVATE_PROGRAM) {
            $view = '_form-private-lesson';
            if (!empty($model->privateLesson->id)) {
                $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);
            } else {
                $privateLessonModel = new PrivateLesson();
            }

            if ($privateLessonModel->load(Yii::$app->request->post())) {
                $privateLessonModel->lessonId = $model->id;
                if (!empty($privateLessonModel->expiryDate)) {
                    $expiryDate = \DateTime::createFromFormat('d-m-Y g:i A', $privateLessonModel->expiryDate);
                    $privateLessonModel->expiryDate = $expiryDate->format('Y-m-d H:i:s');
                } else {
                    $privateLessonModel->expiryDate = $model->date;
                    $privateLessonModel->save();

                    return $this->redirect(['view', 'id' => $model->id]);
                }
                $privateLessonModel->save();
            }
            $data = ['model' => $model, 'privateLessonModel' => $privateLessonModel];
        }
        if ($model->load(Yii::$app->request->post())) {
            $duration = \DateTime::createFromFormat('H:i', $model->duration);
            $model->duration = $duration->format('H:i:s');
            if (empty($model->date)) {
                $model->date = $model->getOldAttribute('date');
                $model->status = Lesson::STATUS_CANCELED;
                $model->save();
                $redirectionLink = $this->redirect(['view', 'id' => $model->id]);
            } else {
                $oldDate = $model->getOldAttribute('date');
                if (new \DateTime($oldDate) != new \DateTime($model->date)) {
                    $model->setScenario(Lesson::SCENARIO_PRIVATE_LESSON);
                    $validate = $model->validate();
                }
                $lessonConflicts = [];
                $lessonConflicts = $model->getErrors('date');
                $lessonConflicts = current($lessonConflicts);
                if (!empty($lessonConflicts)) {
                    if (isset($lessonConflicts['lessonIds']) || isset($lessonConflicts['dates'])) {
                        if (!empty($lessonConflicts['lessonIds'])) {
                            $message = 'Reschedule time conflicts with another lesson';
                        } elseif ($lessonConflicts['dates']) {
                            $message = 'Reschedule Date conflicts with holiday';
                        }
                    } else {
                        $message = $lessonConflicts;
                    }
                    Yii::$app->session->setFlash('alert',
                        [
                        'options' => ['class' => 'alert-danger'],
                        'body' => $message,
                    ]);
                    $redirectionLink = $this->redirect(['update', 'id' => $model->id]);
                } else {
                    $lessonDate = \DateTime::createFromFormat('d-m-Y g:i A', $model->date);
                    $model->date = $lessonDate->format('Y-m-d H:i:s');
                    $model->save();
                    $redirectionLink = $this->redirect(['view', 'id' => $model->id]);
                }
            }

            return $redirectionLink;
        }

        return $this->render($view, $data);
    }
    /**
     * Deletes an existing Lesson model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['review', 'courseId' => $model->courseId]);
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
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = Lesson::find()->location($locationId)
            ->where(['lesson.id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateField()
    {
		$request = Yii::$app->request;
		$response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if ($request->post('hasEditable')) {
			$lessonId = $request->post('editableKey');
            $lessonIndex = $request->post('editableIndex');
            $model = Lesson::findOne(['id' => $lessonId]);
            $model->setScenario(Lesson::SCENARIO_PRIVATE_LESSON);
			$existingDate = $model->date;
            $result = [
                'output' => '',
                'message' => '',
            ];
			$posted = current($_POST['Lesson']);
        	$post = ['Lesson' => $posted];
            if ($model->load($post)) {
				if (! empty($posted['date'])) {
					$lessonTime = (new \DateTime($existingDate))->format('H:i:s');
					$timebits = explode(':', $lessonTime);
					$changedDate = new \DateTime($posted['date']);
					$changedDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
					$model->date = $changedDate->format('Y-m-d H:i:s');
					$output = Yii::$app->formatter->asDate($model->date);
            	}
				if (!empty($posted['time'])) {
					$existingDate = (new \DateTime($existingDate))->format('Y-m-d');
					$existingDate = new \DateTime($existingDate);
					$changedTime = new \DateTime($posted['time']);
					$lessonTime = $changedTime->format('H:i:s');
					$timebits = explode(':', $lessonTime);
					$existingDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
					$model->date = $existingDate->format('Y-m-d H:i:s');
					$newTime = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
					$output = Yii::$app->formatter->asTime($newTime);
				}
				if (!empty($posted['duration'])) {
					$model->duration = $posted['duration'];
					$output = $model->duration;
				}

             $success = $model->save();
			 $errors = $model->errors;
			if (!$success) {
                foreach ($errors as $error) {
                    $message = $error[0];
                }
            }
            $result = [
                'output' => $output,
                'message' => $message,
            ];
		}
            return $result;
        }
    }

    public function actionReview($courseId)
    {
        $request = Yii::$app->request;
        $vacationRequest = $request->get('Vacation');
        $courseRequest = $request->get('Course');
        $lessonFromDate = $courseRequest['lessonFromDate'];
        $lessonToDate = $courseRequest['lessonToDate'];
        $vacationId = $vacationRequest['id'];
        $vacationType = $vacationRequest['type'];
        $courseModel = Course::findOne(['id' => $courseId]);
        $draftLessons = Lesson::find()
            ->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $draftLesson->setScenario('review');
        }
        Model::validateMultiple($draftLessons);
        $conflicts = [];
        foreach ($draftLessons as $draftLesson) {
            $conflicts[$draftLesson->id] = $draftLesson->getErrors('date');
        }
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()->indexBy('id')
                ->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
                ->orderBy(['lesson.date' => SORT_ASC]),
        ]);

        return $this->render('_review', [
            'courseModel' => $courseModel,
            'courseId' => $courseId,
            'lessonDataProvider' => $lessonDataProvider,
            'conflicts' => $conflicts,
            'lessonFromDate' => $lessonFromDate,
            'lessonToDate' => $lessonToDate,
			'vacationId' => $vacationId,
			'vacationType' => $vacationType,
        ]);
    }

    public function actionFetchConflict($courseId)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $courseModel = Course::findOne(['id' => $courseId]);
        $draftLessons = Lesson::find()
            ->where(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $draftLesson->setScenario('review');
        }
        Model::validateMultiple($draftLessons);
        $conflicts = [];
        foreach ($draftLessons as $draftLesson) {
            $conflicts[$draftLesson->id] = $draftLesson->getErrors('date');
        }
        $hasConflict = false;
        foreach ($conflicts as $conflictLessons) {
            foreach ($conflictLessons as $conflictLesson) {
                if ((!empty($conflictLesson['lessonIds'])) || (!empty($conflictLesson['dates']))) {
                    $hasConflict = true;
                    break;
                }
            }
        }

        return [
            'hasConflict' => $hasConflict,
        ];
    }

    public function actionConfirm($courseId)
    {
        $courseModel = Course::findOne(['id' => $courseId]);
        $lessons = Lesson::findAll(['courseId' => $courseModel->id, 'status' => Lesson::STATUS_DRAFTED]);
        $request = Yii::$app->request;
        if (!empty($courseModel->enrolment)) {
            $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
            $enrolmentModel->isConfirmed = true;
            $enrolmentModel->save();
        }
        $courseRequest = $request->get('Course');
        $vacationRequest = $request->get('Vacation');
        $lessonFromDate = $courseRequest['lessonFromDate'];
        $lessonToDate = $courseRequest['lessonToDate'];
        $vacationId = $vacationRequest['id'];
        $vacationType = $vacationRequest['type'];
		if(! empty($vacationId)) {
			$vacation = Vacation::findOne(['id' => $vacationId]);
			$fromDate = (new \DateTime($vacation->fromDate))->format('Y-m-d');
			$toDate = (new \DateTime($vacation->toDate))->format('Y-m-d');
			if($vacationType === Vacation::TYPE_CREATE) {
				$vacation->isConfirmed = true;
				$vacation->save();
				$oldLessons = Lesson::find()
				->where(['courseId' => $courseId])
				->andWhere(['>', 'lesson.date', $fromDate])
				->all();
				$oldLessonIds = [];
				foreach ($oldLessons as $oldLesson) {
					$oldLessonIds[] = $oldLesson->id;
					$oldLesson->status = Lesson::STATUS_CANCELED;
					$oldLesson->save();
				}
			} else {
				$oldLessons = Lesson::find()
				->where(['courseId' => $courseId])
				->andWhere(['>', 'lesson.date', $toDate])
				->all();
				$oldLessonIds = [];
				foreach ($oldLessons as $oldLesson) {
					$oldLessonIds[] = $oldLesson->id;
					$oldLesson->status = Lesson::STATUS_CANCELED;
					$oldLesson->save();
				}
				$vacation->delete();
			}

			foreach ($lessons as $i => $lesson) {
				$lessonRescheduleModel = new LessonReschedule();
				$lessonRescheduleModel->lessonId = $oldLessonIds[$i];
				$lessonRescheduleModel->rescheduledLessonId = $lesson->id;
				$lessonRescheduleModel->save();
			}
		}
        if (!(empty($lessonFromDate) && empty($lessonToDate))) {
            $lessonFromDate = \DateTime::createFromFormat('d-m-Y', $lessonFromDate);
            $lessonToDate = \DateTime::createFromFormat('d-m-Y', $lessonToDate);
            $oldLessons = Lesson::find()
                ->where(['courseId' => $courseModel->id])
                ->scheduled()
                ->between($lessonFromDate, $lessonToDate)
                ->all();
            $oldLessonIds = [];
            foreach ($oldLessons as $oldLesson) {
                $oldLessonIds[] = $oldLesson->id;
                $oldLesson->delete();
            }
            foreach ($lessons as $i => $lesson) {
                $lesson->id = $oldLessonIds[$i];
                $lesson->status = Lesson::STATUS_SCHEDULED;
                $lesson->save();
            }
        }  else {
            foreach ($lessons as $lesson) {
                $lesson->updateAttributes([
                    'status' => Lesson::STATUS_SCHEDULED,
                ]);
            }
        }
		$isPrivateProgram = (int) $courseModel->program->type === (int) Program::TYPE_PRIVATE_PROGRAM;
		if ($isPrivateProgram) {
			if (!empty($vacationId)) {
				if ($vacationType === Vacation::TYPE_CREATE) {
					$message = 'Vacation has been created successfully';
					$link	 = $this->redirect(['student/view', 'id' => $courseModel->enrolment->student->id, '#' => 'vacation']);
				} else {
					$message = 'Vacation has been deleted successfully';
					$link	 = $this->redirect(['student/view', 'id' => $courseModel->enrolment->student->id, '#' => 'vacation']);
				}
			} else {
				$lessonDate		 = (new \DateTime($lessons[0]->date))->format('d-m-Y');
				$lessonStartDate = new \DateTime($lessons[0]->date);
				$lessonEndDate	 = $lessonStartDate->modify('+1 month');
				$message		 = 'Lessons have been created successfully';
				$link			 = $this->redirect([
					'invoice/create',
					'Invoice[customer_id]' => $courseModel->enrolment->student->customer->id,
					'Invoice[type]' => Invoice::TYPE_PRO_FORMA_INVOICE,
					'LessonSearch[fromDate]' => $lessonDate,
					'LessonSearch[toDate]' => $lessonEndDate->format('d-m-Y'),
					'LessonSearch[courseId]' => $courseModel->id,
				]);
			}
		} else {
			$message = 'Course has been created successfully';
			$link	 = $this->redirect(['course/view', 'id' => $courseId]);
		}
		Yii::$app->session->setFlash('alert', [
			'options' => ['class' => 'alert-success'],
			'body' => $message,
		]);
		return $link;
	}

    public function actionInvoice($id)
    {
        $model = Lesson::findOne(['id' => $id]);
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
        $currentDate = new \DateTime();
        $location_id = Yii::$app->session->get('location_id');

        if($model->invoice) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Invoice has been generated already!.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }

        if ($lessonDate <= $currentDate) {
            $invoice = new Invoice();
            $invoice->user_id = $model->enrolment->student->customer->id;
            $invoice->location_id = $location_id;
            $invoice->type = INVOICE::TYPE_INVOICE;
            $invoice->save();
            $invoice->addLineItem($model);
            $invoice->save();
            $proFormaInvoice = Invoice::find()
                ->select(['invoice.id', 'SUM(payment.amount) as credit'])
                ->proFormaCredit($model->id)
                ->one();

            if (!empty($proFormaInvoice)) {
                if ((float) $proFormaInvoice->credit > (float) $invoice->total) {
                    $paymentAmount = $invoice->total;
                } else {
                    $paymentAmount = $proFormaInvoice->credit;
                }
                $paymentModel = new Payment();
                $paymentModel->amount = $paymentAmount;
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
                $paymentModel->reference = $proFormaInvoice->id;
                $paymentModel->invoiceId = $invoice->id;
                $paymentModel->save();

                $creditPaymentId = $paymentModel->id;
                $paymentModel->id = null;
                $paymentModel->isNewRecord = true;
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
                $paymentModel->invoiceId = $proFormaInvoice->id;
                $paymentModel->reference = $invoice->id;
                $paymentModel->save();

                $debitPaymentId = $paymentModel->id;
                $creditUsageModel = new CreditUsage();
                $creditUsageModel->credit_payment_id = $creditPaymentId;
                $creditUsageModel->debit_payment_id = $debitPaymentId;
                $creditUsageModel->save();
            }
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Invoice has been generated successfully',
            ]);

            return $this->redirect(['invoice/view', 'id' => $invoice->id]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Generate invoice against completed lesson only.',
            ]);

            return $this->redirect(['lesson/view', 'id' => $id]);
        }
    }
}
