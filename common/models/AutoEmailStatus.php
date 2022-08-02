<?php

namespace common\models;


use Yii;


/**
 * This is the model class for table "auto_email_status".
 *
 * @property int $id
 * @property string $notificationType
 */

class AutoEmailStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    // public $notificationType;

    public static function tableName()
    {
        return 'auto_email_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId'],  'required'],
            [['lessonId','notificationType'], 'integer'],
            [['status'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     * @return \common\models\query\AutoEmailStatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AutoEmailStatusQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'notificationType' => 'Notification Type',
            'status' => 'Status',
        ];
    }
    

}
