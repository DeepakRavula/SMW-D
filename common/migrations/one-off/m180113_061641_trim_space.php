<?php

use yii\db\Migration;
use common\models\Blog;
use common\models\CalendarEventColor;

class m180113_061641_trim_space extends Migration
{
    public function up()
    {
		$blogs = Blog::find()->all();
		foreach ($blogs as $blog) {
			$blog->updateAttributes([
				'title' => trim($blog->title),
				'content' => trim($blog->content)
			]);
		}
		$calendarEvents = CalendarEventColor::find()->all();
		foreach ($calendarEvents as $calendarEvent) {
			$calendarEvent->updateAttributes([
				'name' => trim($calendarEvent->name),
				'code' => trim($calendarEvent->code),
				'cssClass' => trim($calendarEvent->cssClass)
			]);
		}
		return false;
    }

    public function down()
    {
        echo "m180113_061641_trim_space cannot be reverted.\n";

        return false;
    }
}
