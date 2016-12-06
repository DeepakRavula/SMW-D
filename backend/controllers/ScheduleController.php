<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Location;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Program;
use common\models\Invoice;
use yii\helpers\Url;
use common\models\Holiday;
use common\models\TeacherAvailability;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class ScheduleController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['staffmember'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Qualification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $locationId = Yii::$app->session->get('location_id');
        $teachersAvailabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->joinWith(['userProfile'])
                ->where(['user_location.location_id' => $locationId]);
            }])
            ->orderBy(['teacher_availability_day.id' => SORT_DESC])
            ->all();
        $teachersAvailabilitiesAllDetails = ArrayHelper::toArray($teachersAvailabilities, [
            'common\models\TeacherAvailability' => [
                'id' => function ($teachersAvailability) {
                    return $teachersAvailability->userLocation->user_id;
                },
                'day',
                'from_time',
                'to_time',
            ],
        ]);

        $teachersAvailabilitiesDetails = ArrayHelper::toArray($teachersAvailabilities, [
            'common\models\TeacherAvailability' => [
                'id' => function ($teachersAvailability) {
                    return $teachersAvailability->userLocation->user_id;
                },
                'day',
                'name' => function ($teachersAvailability) {
                    return $teachersAvailability->teacher->getPublicIdentity();
                },
                'programs' => function ($teachersAvailability) {
                    $qualifications = $teachersAvailability->userLocation->qualifications;
                    $programs = [];
                    foreach ($qualifications as $qualification) {
                        $programs[] = $qualification->program_id;
                    }
                    return $programs;
                },
            ],
        ]);
        $availableTeachersDetails = array_unique($teachersAvailabilitiesDetails, SORT_REGULAR);
        $availableTeachersDetails = array_values($availableTeachersDetails);

        $events = [];
        $holidays = Holiday::find()->all();
        foreach ($holidays as $holiday) {
            $date = new \DateTime($holiday->date);
            $events[] = [
                'resourceId' => '0',
                'title' => '',
                'start' => $holiday->date,
                'end' => $date->format('Y-m-d 23:59:59'),
                'className' => 'holiday',
                'rendering' => 'background'
            ];
        }
        $lessons = [];
        $lessons = Lesson::find()
            ->joinWith(['course' => function ($query) {
                $query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
            }])
            ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
			->notDeleted()
            ->all();
        
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
            $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
            } else {
                $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
            }
            $class = null;
            if($lesson->isEnrolmentFirstlesson()) {
                $class = 'enrolment-first-lesson';
            } elseif (!empty($lesson->proFormaInvoice)) {
                if (in_array($lesson->proFormaInvoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT])) {
                    $class = 'proforma-paid';
                } else {
                    $class = 'proforma-unpaid';
                }
            }

            $events[] = [
                'resourceId' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                'className' => $class,
            ];
        }
        unset($lesson);

        $location = Location::findOne($id = Yii::$app->session->get('location_id'));

        $location->from_time = new \DateTime($location->from_time);
        $fromTime = $location->from_time;
        $from_time = $fromTime->format('H:i:s');

        $location->to_time = new \DateTime($location->to_time);
        $toTime = $location->to_time;
        $to_time = $toTime->format('H:i:s');

        return $this->render('index', ['holidays' => $holidays, 'teachersAvailabilitiesAllDetails' => $teachersAvailabilitiesAllDetails, 'teachersAvailabilitiesDetails' => $teachersAvailabilitiesDetails, 'availableTeachersDetails' => $availableTeachersDetails, 'events' => $events, 'from_time' => $from_time, 'to_time' => $to_time]);
    }
}
