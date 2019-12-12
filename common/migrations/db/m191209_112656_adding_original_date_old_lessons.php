<?php

use yii\db\Migration;
use yii\helpers\Console;
use common\models\Location;
use common\models\Lesson;
/**
 * Class m191209_112656_adding_original_date_old_lessons
 */
class m191209_112656_adding_original_date_old_lessons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Cancelling lessons');
        $lessonCount = 0;
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            Console::output("processing location ".$location->name, Console::FG_GREEN, Console::BOLD);
            $lessons = Lesson::find()
            ->location([$location->id])
            ->andwhere(['<=', 'lesson.id', 200000])
            ->all();
            Console::output("processing lessons between 2,00,000 ", Console::FG_GREEN, Console::BOLD);
            foreach ($lessons as $lesson) {
                Console::output("processing lesson ".$lesson->id, Console::FG_GREEN, Console::BOLD);
                $lesson->updateAttributes(['originalDate' => $lesson->getOriginalDate()]);
    
           }
           Console::output("processing lessons between 2,00,001- 4,00,000 ", Console::FG_GREEN, Console::BOLD);
           $lessons2 = Lesson::find()
           ->location([$location->id])
           ->andwhere(['<=', 'lesson.id', 400000])
           ->andwhere(['>=', 'lesson.id', 200000])
           ->all();
           foreach ($lessons2 as $lesson2) {
            Console::output("processing lesson ".$lesson2->id, Console::FG_GREEN, Console::BOLD);
               $lesson2->updateAttributes(['originalDate' => $lesson2->getOriginalDate()]);
   
          }
          Console::output("processing lessons between 4,00,001- 6,00,000 ", Console::FG_GREEN, Console::BOLD);
          $lessons3 = Lesson::find()
          ->location([$location->id])
          ->andwhere(['<=', 'lesson.id', 400000])
          ->andwhere(['>=', 'lesson.id', 600000])
          ->all();
          foreach ($lessons3 as $lesson3) {
            Console::output("processing lesson ".$lesson3->id, Console::FG_GREEN, Console::BOLD);
              $lesson3->updateAttributes(['originalDate' => $lesson3->getOriginalDate()]);
  
         }
         Console::output("processing lessons between 6,00,001- 8,00,000 ", Console::FG_GREEN, Console::BOLD);
         $lessons4 = Lesson::find()
         ->location([$location->id])
         ->andwhere(['<=', 'lesson.id', 800000])
         ->andwhere(['>=', 'lesson.id', 600000])
         ->all();
         foreach ($lessons4 as $lesson4) {
            Console::output("processing lesson ".$lesson4->id, Console::FG_GREEN, Console::BOLD);
             $lesson4->updateAttributes(['originalDate' => $lesson4->getOriginalDate()]);
 
        }
        Console::output("processing lessons between 8,00,001- 10,00,000 ", Console::FG_GREEN, Console::BOLD);
        $lessons5 = Lesson::find()
        ->location([$location->id])
        ->andwhere(['<=', 'lesson.id', 1000000])
        ->andwhere(['>=', 'lesson.id', 800000])
        ->all();
        foreach ($lessons5 as $lesson5) {
            Console::output("processing lesson  ".$lesson5->id, Console::FG_GREEN, Console::BOLD);
            $lesson5->updateAttributes(['originalDate' => $lesson5->getOriginalDate()]);

       }
       Console::output("processing lessons greater than 10,00,000", Console::FG_GREEN, Console::BOLD);
       $lessons6 = Lesson::find()
       ->location([$location->id])
       ->andwhere(['>', 'lesson.id', 1000000])
       ->all();
       foreach ($lessons6 as $lesson6) {
           Console::output("processing lesson  ".$lesson6->id, Console::FG_GREEN, Console::BOLD);
           $lesson6->updateAttributes(['originalDate' => $lesson6->getOriginalDate()]);

      }
        }
        Console::output("Affected Lessons Count".$lessonCount, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191209_112656_adding_original_date_old_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_112656_adding_original_date_old_lessons cannot be reverted.\n";

        return false;
    }
    */
}
