<?php

namespace common\models\timelineEvent;

use Yii;
use common\models\Invoice;

/**
 * This is the model class for table "timeline_event_invoice".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $invoiceId
 * @property string $action
 */
class TimelineEventInvoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timelineEventId', 'invoiceId', 'action'], 'required'],
            [['timelineEventId', 'invoiceId'], 'integer'],
            [['action'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timelineEventId' => 'Timeline Event ID',
            'invoiceId' => 'Invoice ID',
            'action' => 'Action',
        ];
    }

	public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId']);
    }
}