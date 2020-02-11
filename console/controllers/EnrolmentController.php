<?php

namespace console\controllers;

use Yii;
use Carbon\Carbon;
use common\models\AutoRenewal;
use common\models\AutoRenewalLessons;
use common\models\AutoRenewalPaymentCycle;
use common\models\User;
use common\models\Lesson;
use common\models\Enrolment;
use yii\console\Controller;
use common\models\Course;
use common\models\CourseProgramRate;
use common\models\CourseSchedule;
use common\models\CourseScheduleOldTeacher;
use common\models\LessonConfirm;
use common\models\LessonOldTeacher;
use common\models\Location;
use common\models\Qualification;
use common\models\TeacherAvailability;

class EnrolmentController extends Controller
{
    public $id;

    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            $actionID == 'delete' || 'set-lesson-due-date' ? ['id'] : []
        );
    }

    public function actionAutoRenewal()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $priorDate = (new Carbon())->addDays(Enrolment::AUTO_RENEWAL_DAYS_FROM_END_DATE);
        $courses = Course::find()
            ->regular()
            ->confirmed()
            ->needToRenewal($priorDate)
            ->privateProgram()
            ->notDeleted()
            ->all();
        foreach ($courses as $course) {
            $autoRenewal = new AutoRenewal();
            $autoRenewal->renewEnrolment($course);
        }
    }

    public function actionDelete()
    {
        $model = Enrolment::findOne($this->id);
        return $model->deleteWithTransactionalData();
    }

    public function actionFixEnrolmentLessons()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [];
        $locations = Location::find()->notDeleted()->all();
        foreach ($locations as $location) {
            print_r("\n" . $location->name);
            print_r("\n------------------"); 
        $courses = Course::find()
            ->regular()
            ->confirmed()
            ->location($location->id)
            ->privateProgram()
            ->notDeleted()
            ->all();
        $count = 0;
        if ($courses) {
            foreach ($courses as $course) {
                if (count($course->courseSchedules) > 2) {
                    $firstLesson = Lesson::find()
                        ->andWhere(['courseId' => $course->id])
                        ->orderBy(['lesson.date' => SORT_ASC])
                        ->notCanceled()
                        ->notDeleted()
                        ->isConfirmed()
                        ->notRescheduled()
                        ->regular()
                        ->one();
                    $lesson1 = Lesson::find()
                        ->andWhere(['courseId' => $course->id])
                        ->andWhere(['<=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                        ->orderBy(['lesson.id' => SORT_DESC])
                        ->notCanceled()
                        ->notDeleted()
                        ->isConfirmed()
                        ->notRescheduled()
                        ->regular()
                        ->one();
                    $lesson2 = Lesson::find()
                        ->andWhere(['courseId' => $course->id])
                        ->andWhere(['>=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                        ->notCanceled()
                        ->notDeleted()
                        ->isConfirmed()
                        ->notRescheduled()
                        ->regular()
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->one();
                    if ($lesson1 && $lesson2) {
                        if ($lesson1->teacherId != $lesson2->teacherId && $lesson2->teacherId == $firstLesson->teacherId) {
                            //print_r("\nCourse:".$course->id."Enrolment:".$course->enrolment->id."Lesson 1:".$lesson1->id."Teacher id:".$lesson1->teacherId."Lesson 2:".$lesson2->id."Teacher id:".$lesson2->teacherId."\n");
                            print_r("\nhttps://smw.arcadiamusicacademy.com/admin/".$location->slug."/enrolment/view?id=" . $course->enrolment->id);
                        }
                    }
                }
            }
        }
    }
    }

    public function actionChangeTeacherForAutoRenewalLessons()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [4, 9, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22];
        $locations = Location::find()->notDeleted()->andWhere(['IN', 'location.id', $locationIds])->all();
        foreach ($locations as $location) {
            print_r("\n" . $location->name);
            print_r("\n------------------");
            $courses = Course::find()
                ->regular()
                ->location($location->id)
                ->confirmed()
                ->privateProgram()
                ->notDeleted()
                ->all();
            $count = 0;
            if ($courses) {
                foreach ($courses as $course) {
                    if (count($course->courseSchedules) > 2) {
                        $firstLesson = Lesson::find()
                            ->andWhere(['courseId' => $course->id])
                            ->orderBy(['lesson.date' => SORT_ASC])
                            ->notCanceled()
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->one();
                        $lesson1 = Lesson::find()
                            ->andWhere(['courseId' => $course->id])
                            ->andWhere(['<=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                            ->orderBy(['lesson.id' => SORT_DESC])
                            ->notCanceled()
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->one();
                        $lesson2 = Lesson::find()
                            ->andWhere(['courseId' => $course->id])
                            ->andWhere(['>=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                            ->notCanceled()
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->orderBy(['lesson.id' => SORT_ASC])
                            ->one();
                        if ($lesson1 && $lesson2) {
                            if ($lesson1->teacherId != $lesson2->teacherId && $lesson2->teacherId == $firstLesson->teacherId) {
                                $autoRenewalLessonQuery = Lesson::find()
                                    ->andWhere(['courseId' => $course->id])
                                    ->joinWith(['autoRenewalLessons' => function ($query) {
                                        $query->andWhere(['NOT', ['auto_renewal_lessons.lessonId' => null]]);
                                    }])
                                    ->notDeleted()
                                    ->notCanceled()
                                    ->notRescheduled()
                                    ->orderBy(['lesson.id' => SORT_ASC]);
                                $autoRenewalFirstLesson = $autoRenewalLessonQuery->one();
                                $lessons = $autoRenewalLessonQuery->all();
                                if ($autoRenewalFirstLesson) {
                                    $lastLessonBeforeAutoRenewal = Lesson::find()
                                        ->andWhere(['courseId' => $course->id])
                                        ->andWhere(['<', 'DATE(lesson.date)', Carbon::parse($autoRenewalFirstLesson->date)->format('Y-m-d')])
                                        ->notDeleted()
                                        ->notCanceled()
                                        ->notRescheduled()
                                        ->orderBy(['lesson.date' => SORT_DESC])
                                        ->one();
                                    if ($lessons) {
                                        foreach ($lessons as $lesson) {
                                            $lessonOldTeacher = new LessonOldTeacher();
                                            $lessonOldTeacher->lessonId = $lesson->id;
                                            $lessonOldTeacher->teacherId = $lesson->teacherId;
                                            $lessonOldTeacher->rate = $lesson->teacherRate;
                                            $lessonOldTeacher->courseId = $course->id;
                                            $lessonOldTeacher->enrolmentId = $lesson->enrolment->id;
                                            $lessonOldTeacher->createdByUserId = Yii::$app->user->id;
                                            if (!$lessonOldTeacher->save()) {
                                                print_r($lessonOldTeacher->getErrors());
                                            }
                                            $lesson->updateAttributes(['teacherId' => $lastLessonBeforeAutoRenewal->teacherId]);
                                            // $qualification = Qualification::findOne(['teacher_id' => $lesson->teacherId,
                                            // 'program_id' => $lesson->course->program->id]);
                                            // $teacherRate = !empty($qualification->rate) ? $qualification->rate : 0;
                                            // //$lesson->updateAttributes(['teacherRate' => $teacherRate]);
                                          
                                        }

                                        $recentCourseSchedule = $course->recentCourseSchedule;
                                        $oldCourseSchedule = new CourseScheduleOldTeacher();
                                        $oldCourseSchedule->teacherId = $recentCourseSchedule->teacherId;
                                        $oldCourseSchedule->courseScheduleId = $recentCourseSchedule->id;
                                        $oldCourseSchedule->courseId = $recentCourseSchedule->courseId;
                                        $oldCourseSchedule->createdByUserId = Yii::$app->user->id;
                                        $oldCourseSchedule->isAdded = false;
                                        $oldCourseSchedule->endDate = $recentCourseSchedule->endDate;
                                        if (!$oldCourseSchedule->save()) {
                                            print_r($oldCourseSchedule->getErrors());
                                        }
                                        $recentCourseSchedule->teacherId = $lastLessonBeforeAutoRenewal->teacherId;
                                        $recentCourseSchedule->save();
                                        print_r("\nhttps://smw.arcadiamusicacademy.com/admin/" . $location->slug . "/enrolment/view?id=" . $course->enrolment->id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function actionChangeTeacherForUserExtendedLessons()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [4, 9, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22];
        $locations = Location::find()->notDeleted()->andWhere(['IN', 'location.id', $locationIds])->all();
        foreach ($locations as $location) {
            print_r("\n" . $location->name);
            print_r("\n------------------");
            $courses = Course::find()
                ->regular()
                ->location($location->id)
                ->confirmed()
                ->privateProgram()
                ->notDeleted()
                ->all();
            $count = 0;
            if ($courses) {
                foreach ($courses as $course) {
                    if (count($course->courseSchedules) > 2) {
                        $firstLesson = Lesson::find()
                            ->andWhere(['courseId' => $course->id])
                            ->orderBy(['lesson.date' => SORT_ASC])
                            ->notCanceled()
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->one();
                        $lesson1 = Lesson::find()
                            ->andWhere(['courseId' => $course->id])
                            ->andWhere(['<=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                            ->orderBy(['lesson.id' => SORT_DESC])
                            ->notCanceled()
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->one();
                        $lesson2Query = Lesson::find()
                            ->andWhere(['courseId' => $course->id])
                            ->andWhere(['>=', 'DATE(lesson.date)', Carbon::parse($course->recentCourseSchedule->startDate)->format('Y-m-d')])
                            ->notCanceled()
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->orderBy(['lesson.id' => SORT_ASC]);
                        $lesson2 = $lesson2Query->one();
                        
                        if ($lesson1 && $lesson2) {
                            if ($lesson1->teacherId != $lesson2->teacherId && $lesson2->teacherId == $firstLesson->teacherId) {
                                $lessons = $lesson2Query->all();
                               
                                    if ($lessons) {
                                        foreach ($lessons as $lesson) {
                                            $lessonOldTeacher = new LessonOldTeacher();
                                            $lessonOldTeacher->lessonId = $lesson->id;
                                            $lessonOldTeacher->teacherId = $lesson->teacherId;
                                            $lessonOldTeacher->courseId = $course->id;
                                            $lessonOldTeacher->enrolmentId = $lesson->enrolment->id;
                                            $lessonOldTeacher->createdByUserId = Yii::$app->user->id;
                                            $lessonOldTeacher->save();
                                            $lesson->updateAttributes(['teacherId' => $lesson1->teacherId]);
                                            $qualification = Qualification::findOne(['teacher_id' => $lesson->teacherId,
                                            'program_id' => $lesson->course->program->id]);
                                            $teacherRate = !empty($qualification->rate) ? $qualification->rate : 0;
                                            $lesson->updateAttributes(['teacherRate' => $teacherRate]);
                                          
                                        }
                                        $recentCourseSchedule = $course->recentCourseSchedule;
                                        $oldCourseSchedule = new CourseScheduleOldTeacher();
                                        $oldCourseSchedule->teacherId = $recentCourseSchedule->teacherId;
                                        $oldCourseSchedule->courseId = $recentCourseSchedule->courseId;
                                        $oldCourseSchedule->courseScheduleId = $recentCourseSchedule->id;
                                        $oldCourseSchedule->createdByUserId = Yii::$app->user->id;
                                        $oldCourseSchedule->endDate = $recentCourseSchedule->endDate;
                                        $oldCourseSchedule->isAdded = false;
                                        $oldCourseSchedule->save();
                                        $recentCourseSchedule->updateAttributes(['teacherId' => $lesson1->teacherId]);
                                        
                                        print_r("\nhttps://smw.arcadiamusicacademy.com/admin/" . $location->slug . "/enrolment/view?id=" . $course->enrolment->id);
                                    }
                            }
                        }
                    }
                }
            }
        }
    }

    public function actionAddCourseScheduleForMissingEnrolments()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [4, 9, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22];
        $locations = Location::find()->notDeleted()->andWhere(['IN', 'location.id', $locationIds])->all();
        foreach ($locations as $location) {
            print_r("\n" . $location->name);
            print_r("\n------------------");
            $courses = Course::find()
                ->regular()
                ->location($location->id)
                ->confirmed()
                ->privateProgram()
                ->notDeleted()
                ->all();
            $count = 0;
            if ($courses) {
                foreach ($courses as $course) {
                    if (Carbon::parse($course->recentCourseSchedule->endDate)->format('Y-m-d') < Carbon::parse($course->endDate)->format('Y-m-d')) {
                            if (Carbon::parse($course->endDate)->diffInDays(Carbon::parse($course->recentCourseSchedule->endDate)) > 30) {
                        $courseSchedule = new CourseSchedule();
                        $courseSchedule->courseId = $course->id;
                        $courseSchedule->day = $course->recentCourseSchedule->day;
                        $courseSchedule->fromTime = $course->recentCourseSchedule->fromTime;
                        $courseSchedule->duration = $course->recentCourseSchedule->duration;
                        $oldCourseSchedules = $course->courseSchedules;
                        if ($oldCourseSchedules) {
                        $oldCourseSchedule = end($oldCourseSchedules);
                        $courseSchedule->startDate = Carbon::parse($oldCourseSchedule->endDate)->modify('+1days')->format('Y-m-d H:i:s');
                        } else {
                        $courseSchedule->startDate = $course->startDate;
                        }
                        $courseSchedule->endDate = Carbon::parse($course->endDate)->format('Y-m-d H:i:s');    
                        $courseSchedule->teacherId = $course->recentCourseSchedule->teacherId;
                        if (!$courseSchedule->save()) {
                            print_r($courseSchedule->getErrors());
                        }
                        $oldCourseScheduleEntry = new CourseScheduleOldTeacher();
                        $oldCourseScheduleEntry->teacherId = $courseSchedule->teacherId;
                        $oldCourseScheduleEntry->courseId = $courseSchedule->courseId;
                        $oldCourseScheduleEntry->courseScheduleId = $courseSchedule->id;
                        $oldCourseScheduleEntry->createdByUserId = Yii::$app->user->id;
                        $oldCourseScheduleEntry->endDate = $courseSchedule->endDate;
                        $oldCourseScheduleEntry->isAdded = true;
                        if (!$oldCourseScheduleEntry->save()) {
                            print_r("\nsssss");
                            print_r($oldCourseScheduleEntry->getErrors());
                        }
                       
                    } else {
                        $recentCourseSchedule = $course->recentCourseSchedule;                     
                        $oldCourseScheduleEntry = new CourseScheduleOldTeacher();
                        $oldCourseScheduleEntry->teacherId = $recentCourseSchedule->teacherId;
                        $oldCourseScheduleEntry->courseId = $recentCourseSchedule->courseId;
                        $oldCourseScheduleEntry->courseScheduleId = $recentCourseSchedule->id;
                        $oldCourseScheduleEntry->createdByUserId = Yii::$app->user->id;
                        $oldCourseScheduleEntry->endDate = $recentCourseSchedule->endDate;
                        $oldCourseScheduleEntry->isAdded = false;
                        $recentCourseSchedule->endDate = Carbon::parse($course->endDate)->format('Y-m-d H:i:s');
                        $recentCourseSchedule->save();
                        $oldCourseScheduleEntry->save();
                    }
                    }
                }            
        }
    }
}
}
