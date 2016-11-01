<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\filters\AccessControl;
use common\models\Program;
use common\models\Invoice;
use yii\helpers\Url;
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
        $teachersWithClass = TeacherAvailability::find()
            ->select(['user_location.user_id as id', "CONCAT(user_profile.firstname, ' ', user_profile.lastname) as name"])
            ->distinct()
            ->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->joinWith(['userProfile' => function ($query) {
                    $query->joinWith(['lesson' => function ($query) {
                        $query->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]]);
                    }]);
                }])
                ->where(['user_location.location_id' => $locationId]);
            }])
            ->orderBy(['teacher_availability_day.id' => SORT_DESC])
            ->all();

        $activeTeachers = [];
        foreach ($teachersWithClass as $teacherWithClass) {
            $activeTeachers[] = [
                    'id' => $teacherWithClass->id,
                    'name' => $teacherWithClass->name,
                ];
        }

        $allTeachers = TeacherAvailability::find()
                ->select(['user_location.user_id as id', "CONCAT(user_profile.firstname, ' ', user_profile.lastname) as name"])
                ->distinct()
                ->joinWith(['userLocation' => function ($query) use ($locationId) {
                    $query->joinWith(['userProfile' => function ($query) {
                    }])
                    ->where(['user_location.location_id' => $locationId]);
                }])
                ->orderBy(['teacher_availability_day.id' => SORT_DESC])
                ->all();

        $availableTeachers = [];
        foreach ($allTeachers as $allTeacher) {
            $availableTeachers[] = [
                        'id' => $allTeacher->id,
                        'name' => $allTeacher->name,
                    ];
        }

        $lessons = [];
        $lessons = Lesson::find()
            ->joinWith(['course' => function ($query) {
                $query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
            }])
            ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
            ->all();
        $events = [];
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
            if (!empty($lesson->proFormaInvoice)) {
                if (in_array($lesson->proFormaInvoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT])) {
                    $class = 'proforma-paid';
                } else {
                    $class = 'proforma-unpaid';
                }
            }
            $events[] = [
                'resources' => $lesson->teacherId,
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

        return $this->render('index', ['teachersWithClass' => $activeTeachers, 'allTeachers' => $availableTeachers, 'events' => $events, 'from_time' => $from_time, 'to_time' => $to_time]);
    }

    public function actionUpdateEvents()
    {
        $data = Yii::$app->request->rawBody;
        $data = Json::decode($data, true);
        $lesson = Lesson::findOne(['id' => $data['id']]);
        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lesson->date);
        $rescheduledLessonDate = clone $lessonDate;
        if ((float) $data['minutes'] > 0) {
            $rescheduledLessonDate->add(new \DateInterval('PT'.round($data['minutes']).'M'));
        } else {
            $rescheduledLessonDate->sub(new \DateInterval('PT'.round(abs($data['minutes'])).'M'));
        }
        $lesson->date = $rescheduledLessonDate->format('Y-m-d H:i:s');
        $lesson->save();
    }
}
