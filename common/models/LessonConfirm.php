<?php

namespace common\models;

use yii\base\Model;
use Yii;
use common\models\log\StudentLog;
use Carbon\Carbon;
/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $startDate
 * @property string $endDate
 */
class LessonConfirm extends Model
{
    public $enrolmentType;
    public $rescheduleBeginDate;
    public $rescheduleEndDate;
    public $enrolmentIds;
    public $courseId;
    public $teacherId;
    public $changesFrom;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enrolmentType', 'rescheduleBeginDate', 'rescheduleEndDate', 'enrolmentIds', 'courseId',
                'changesFrom', 'teacherId'], 'safe']
        ];
    }

    public function confirmBulkReschedule()
    {
        $courseModel = Course::findOne($this->courseId);
        $lessons = Lesson::find()
            ->andWhere(['courseId' => $courseModel->id, 'isConfirmed' => false])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $oneLesson = end($lessons);
        $startDate = new \DateTime($this->rescheduleBeginDate);
        $endDate = new \DateTime($this->rescheduleEndDate);
        $lessonIds = [];
        $oldRescheduledLessons = Lesson::find()
            ->andWhere(['courseId' => $courseModel->id])
            ->notDeleted()
            ->isConfirmed() 
            ->rescheduled()
            ->notCanceled()
            ->unInvoiced()
            ->andWhere(['>=', 'DATE(lesson.date)', $startDate->format('Y-m-d')])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $dateToChangeSchedule = $startDate->format('Y-m-d H:i:s');
        foreach ($oldRescheduledLessons as $oldRescheduledLesson) {
            if ($oldRescheduledLesson->getOriginalDate() < $dateToChangeSchedule ) {
                $lessonIds[] = $oldRescheduledLesson->id;
            }
        }
        $oldLessons = Lesson::find()
            ->andWhere(['courseId' => $courseModel->id])
            ->notDeleted()
            ->isConfirmed() 
            ->notCanceled()
            ->unInvoiced()
            ->andWhere(['NOT', ['lesson.id' => $lessonIds]])
            ->andWhere(['>=', 'DATE(lesson.date)', $startDate->format('Y-m-d')])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        
        $courseDate = (new \DateTime($courseModel->endDate))->format('d-m-Y');
        if ($endDate->format('d-m-Y') == $courseDate && !empty($oneLesson)) {
            $courseModel->updateAttributes([
                'teacherId' => $oneLesson->teacherId,
            ]);
        }
        foreach ($lessons as $i => $lesson) {
            $oldLesson = $oldLessons[$i];
            $oldLesson->cancel();
            $oldLesson->rescheduleTo($lesson, $this->rescheduleBeginDate);
            $bulkReschedule = new BulkRescheduleLesson();
            $bulkReschedule->lessonId = $lesson->id;
            $bulkReschedule->save();
        }
        if ($lessons) {
            $lessonModel = end($lessons);
            $this->createCourseSchedule($lessonModel, $startDate, $endDate);
        }
        return true;
    }

    public function manageHolidayLessons()
    {
        $courseModel = Course::findOne(['id' => $this->courseId]);
        $courseModel->updateAttributes([
            'isConfirmed' => true
        ]);
        $holidayConflictedLessonIds = $courseModel->getHolidayLessons();
        $holidayLessons = Lesson::findAll(['id' => $holidayConflictedLessonIds]);
        foreach ($holidayLessons as $holidayLesson) {
            $holidayLesson->updateAttributes([
                'status' => Lesson::STATUS_UNSCHEDULED
            ]);
        }
        return true;
    }

    public function confirmEnrolment()
    {
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $courseModel = Course::findOne(['id' => $this->courseId]);
        $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
        $enrolmentModel->isConfirmed = true;
        $enrolmentModel->save();
        $enrolmentModel->setPaymentCycle($enrolmentModel->enrolmentPaymentFrequency->paymentCycleStartDate);
        if ($enrolmentModel->course->isPrivate()) {
            $enrolmentModel->course->updateDates();
        }
        $enrolmentModel->on(Enrolment::EVENT_AFTER_INSERT, [new StudentLog(), 'addEnrolment'],
            ['loggedUser' => $loggedUser]);
        $enrolmentModel->customer->updateCustomerBalance();
        $enrolmentModel->setDueDate();
        return true;
    }

    public function confirmCustomer()
    {
        $courseModel = Course::findOne($this->courseId);
        $enrolmentModel = Enrolment::findOne(['id' => $courseModel->enrolment->id]);
        $enrolmentModel->setStatus();
        $enrolmentModel->trigger(Enrolment::EVENT_AFTER_INSERT);
        return true;
    }

    public function confirmEnrolmentTeacherChange($enrolmentId)
    {
     
        $changesFrom = (new \DateTime($this->changesFrom))->format('Y-m-d');

        $oldLessons = Lesson::find()
            ->notDeleted()
            ->andWhere(['>=', 'DATE(`lesson`.`date`)', $changesFrom])
            ->enrolment($enrolmentId)
            ->notCanceled()
            ->orderBy(['lesson.date' => SORT_ASC])
            ->isConfirmed()
            ->all();
            
        $lessons = Lesson::find()
            ->notDeleted()
            ->andWhere(['>=', 'DATE(lesson.date)', $changesFrom])
            ->enrolment($enrolmentId)
            ->notCanceled()
            ->orderBy(['lesson.date' => SORT_ASC])
            ->notConfirmed()
            ->all();
           
        foreach ($lessons as $i => $lesson) {
            $oldLesson = Lesson::findOne($oldLessons[$i]->id);
            $oldLesson->cancel();
            $oldLesson->rescheduleTo($lesson, null);
            $bulkReschedule = new BulkRescheduleLesson();
            $bulkReschedule->lessonId = $lesson->id;
            $bulkReschedule->save();
            if ($lesson->course->teacherId != $this->teacherId) {
                $lesson->course->updateAttributes(['teacherId' => $this->teacherId]);
            }
        }
        $startDate = Carbon::parse($changesFrom);
        
       
        if ($lessons) {
            $lessonModel = end($lessons);
            $endDate = Carbon::parse($lessonModel->course->endDate);
            $this->createCourseSchedule($lessonModel, $startDate, $endDate);
        }
        return true;
    }

    public function createCourseSchedule($lessonModel, $startDate, $endDate)
     {
        $lastEndDate = $startDate;
        $lastEndDate = $lastEndDate->modify('-1days');
        $lessonModel->course->recentCourseSchedule->updateAttributes([
            'endDate' => ($lastEndDate)->format('Y-m-d'),
        ]);
        $courseSchedule = new CourseSchedule();
        $courseSchedule->courseId = $lessonModel->course->id;
        $courseSchedule->fromTime = Carbon::parse($lessonModel->date)->format('H:i:s');
        $courseSchedule->duration = Carbon::parse($lessonModel->duration)->format('H:i:s');
        $courseSchedule->day = Carbon::parse($lessonModel->date)->format('N');
        $oldCourseSchedules = $lessonModel->course->courseSchedules;
        if ($oldCourseSchedules) {
        $oldCourseSchedule = end($oldCourseSchedules);
        $courseSchedule->startDate = Carbon::parse($oldCourseSchedule->endDate)->modify('+1days')->format('Y-m-d H:i:s');
        } else {
        $courseSchedule->startDate = $lessonModel->course->startDate;
        }
        $courseSchedule->endDate = $endDate->format('Y-m-d H:i:s');    
        $courseSchedule->teacherId = $lessonModel->teacherId;
        $courseSchedule->save();
    }
}
