<?php

namespace common\models\log;

use Yii;

/**
 * This is the model class for table "log_object".
 *
 * @property integer $id
 * @property string $name
 */
class LogObject extends \yii\db\ActiveRecord
{
    const TYPE_COURSE    = 'course';
    const TYPE_ENROLMENT = 'enrolment';
    const TYPE_STUDENT   = 'student';
    const TYPE_INVOICE   = 'invoice';
    const TYPE_PAYMENT   = 'payment';
    const TYPE_USER      = 'user';
    const TYPE_LESSON    = 'lesson';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_object';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
