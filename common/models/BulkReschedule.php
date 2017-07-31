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
class BulkReschedule extends \yii\db\ActiveRecord
{
	const TYPE_VACATION_CREATE = 1;
	const TYPE_VACATION_DELETE = 2;
	const TYPE_RESCHEDULE_BULK_LESSONS = 3;
	const TYPE_RESCHEDULE_FUTURE_LESSONS = 4;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bulk_reschedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type'
        ];
    }
}
