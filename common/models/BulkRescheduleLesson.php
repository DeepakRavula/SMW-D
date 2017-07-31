<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "note".
 *
 * @property string $id
 * @property string $instanceId
 * @property integer $instanceType
 * @property string $content
 * @property string $createdUserId
 * @property string $createdOn
 * @property string $updatedOn
 */
class BulkRescheduleLesson extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bulk_reschedule_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bulkRescheduleId', 'lessonId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bulkRescheduleId' => 'Bulk Reschedule Id'
        ];
    }
}