<?php

namespace common\behaviors;

use yii;

class ClosureTableQuery extends \valentinek\behaviors\ClosureTableQuery
{
    public function roots()
    {
        $query = $this->owner;
        $modelClass = $query->modelClass;
        $db = $modelClass::getDb();
        $primaryKeyName = $db->quoteColumnName($modelClass::primaryKey()[0]);
        $childAttribute = $db->quoteColumnName($this->childAttribute);
        $parentAttribute = $db->quoteColumnName($this->parentAttribute);
        $query->leftJoin(
            $this->tableName . ' as ct1',
            $modelClass::tableName() . '.' . $primaryKeyName . "=ct1." . $childAttribute
        );
        $query->leftJoin(
            $this->tableName . ' as ct2',
            'ct1.' . $childAttribute . '=ct2.' . $childAttribute
            . ' AND ct2.' . $parentAttribute . ' <> ct1.' . $parentAttribute
        );
        $query->andWhere('ct2.' . $parentAttribute . ' IS NULL');
        return $query;
    }
}
