<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "system_log".
 *
 * @property int $id
 * @property int $level
 * @property string $category
 * @property int $log_time
 * @property string $prefix
 * @property int $message
 */
class SystemLog extends \yii\db\ActiveRecord
{
    const CATEGORY_NOTIFICATION = 'notification';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level', 'log_time', 'message'], 'integer'],
            [['log_time'], 'required'],
            [['prefix'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'level' => Yii::t('backend', 'Level'),
            'category' => Yii::t('backend', 'Category'),
            'log_time' => Yii::t('backend', 'Log Time'),
            'prefix' => Yii::t('backend', 'Prefix'),
            'message' => Yii::t('backend', 'Message'),
        ];
    }
}
