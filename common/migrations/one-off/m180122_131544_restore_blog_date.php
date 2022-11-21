<?php

use yii\db\Migration;
use common\models\Blog;

class m180122_131544_restore_blog_date extends Migration
{
    public function up()
    {
        $dates = [
            '2016-12-06 17:47:29',
            '2016-12-13 21:08:37',
            '2016-12-19 18:54:51',
            '2017-01-03 05:42:27',
            '2017-01-04 16:24:35'
        ];
        $blogs = Blog::find()->all();
        foreach ($blogs as $i => $blog) {
            if (!empty($blog)) {
                $blog->updateAttributes([
                    'date' => $dates[$i]
                ]);
            }
        }
    }

    public function down()
    {
        echo "m180122_131544_restore_blog_date cannot be reverted.\n";

        return false;
    }
}
