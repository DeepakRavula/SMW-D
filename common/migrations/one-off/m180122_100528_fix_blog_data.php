<?php

use yii\db\Migration;
use common\models\User;
use common\models\Blog;

/**
 * Class m180122_100528_fix_blog_data
 */
class m180122_100528_fix_blog_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $user = User::find()
            ->joinWith(['primaryEmail' => function ($query) {
                $query->andWhere(['LIKE', 'user_email.email', 'kristin@kristingreen.ca']);
            }])->one();
        $blogs = Blog::find()->all();
        foreach ($blogs as $blog) {
            $blog->updateAttributes([
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180122_100528_fix_blog_data cannot be reverted.\n";

        return false;
    }
}
