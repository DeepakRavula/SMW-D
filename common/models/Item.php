<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property string $id
 * @property string $itemCategoryId
 * @property string $locationId
 * @property string $code
 * @property string $description
 * @property double $price
 * @property integer $royaltyFree
 * @property string $taxStatusId
 * @property integer $status
 */
class Item extends \yii\db\ActiveRecord
{
    const LESSON_ITEM = 'LESSON';

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    const ROYALTY_FREE  = 1;
    const NOT_ROYALTY_FREE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['itemCategoryId', 'locationId', 'code', 'royaltyFree', 'taxStatusId', 'status'], 'required'],
            [['itemCategoryId', 'locationId', 'royaltyFree', 'taxStatusId', 'status'], 'integer'],
            [['price'], 'number'],
            [['code'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'itemCategoryId' => 'Item Category',
            'locationId' => 'Location ID',
            'code' => 'Code',
            'description' => 'Description',
            'price' => 'Price',
            'royaltyFree' => 'Is Royalty Free',
            'taxStatusId' => 'Tax Status',
            'status' => 'status',
        ];
    }

    public static function itemStatuses()
    {
        return [
            self::STATUS_ENABLED => Yii::t('common', 'Enable'),
            self::STATUS_DISABLED => Yii::t('common', 'Disable'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ItemQuery(get_called_class());
    }

    public function getItemCategory()
    {
        return $this->hasOne(ItemCategory::className(), ['id' => 'itemCategoryId']);
    }

    public function getTaxStatus()
    {
        return $this->hasOne(TaxStatus::className(), ['id' => 'taxStatusId']);
    }

    public function getStatusType()
    {
        switch ($this->status) {
            case self::STATUS_ENABLED:
                $status = 'Enable';
            break;
            case self::STATUS_DISABLED:
                $status = 'Disable';
            break;
        }

        return $status;
    }

    public function getRoyaltyFreeStatus()
    {
        switch ($this->status) {
            case self::ROYALTY_FREE:
                $status = 'Yes';
            break;
            case self::NOT_ROYALTY_FREE:
                $status = 'No';
            break;
        }

        return $status;
    }
}