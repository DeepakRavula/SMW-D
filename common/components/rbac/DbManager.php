<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\components\rbac;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use common\components\rbac\Item;
use common\components\rbac\Role;
use common\components\rbac\Permission;
use common\models\Location;

class DbManager extends \yii\rbac\DbManager
{
    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $assignments = $this->getAssignments($userId);
        if ($this->hasNoAssignments($assignments)) {
            return false;
        }

        $this->loadFromCache();
        if ($this->items !== null) {
            return $this->checkAccessFromCache($userId, $permissionName, $params, $assignments);
        } else {
            return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
        }
    }
    protected function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id ?? null;
        
        if (($item = $this->getItem($itemName)) === null) {
            return false;
        }
        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }
        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }
        $query = new Query;
        $parents = $query->select(['parent'])
            ->from($this->itemChildTable)
            ->andWhere(['child' => $itemName, 'location_id' => $locationId])
            ->column($this->db);
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }
    
    public function getLocationSpecificItems($type)
    {
        $query = (new Query())
            ->from($this->itemTable)
            ->andWhere(['type' => $type, 'isLocationSpecific' => true]);

        $items = [];
        foreach ($query->all($this->db) as $row) {
            $items[$row['name']] = $this->populateItem($row);
        }

        return $items;
    }

    /**
     * Populates an auth item with the data fetched from database
     * @param array $row the data from the auth item table
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }
        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
            'location_id' => !empty($row['location_id']) ? $row['location_id'] : null
        ]);
    }

    /**
     * @inheritdoc
     */
    public function addChild($parent, $child)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $this->addChildWithLocation($parent, $child, $locationId);
        return true;
    }
    
    public function addChildWithLocation($parent, $child, $locationId)
    {
        if ($parent->name === $child->name) {
            throw new InvalidParamException("Cannot add '{$parent->name}' as a child of itself.");
        }

        if ($parent instanceof Permission && $child instanceof Role) {
            throw new InvalidParamException('Cannot add a role as a child of a permission.');
        }

        if ($this->detectLoopWithLocation($parent, $child, $locationId)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }

        $this->db->createCommand()
            ->insert($this->itemChildTable, ['parent' => $parent->name, 'child' => $child->name, 'location_id' => $locationId])
            ->execute();

        $this->invalidateCache();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeChild($parent, $child)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $result = $this->db->createCommand()
            ->delete($this->itemChildTable, ['parent' => $parent->name, 'child' => $child->name, 'location_id' => $locationId])
            ->execute() > 0;

        $this->invalidateCache();

        return $result;
    }
    
    public function detectLoopWithLocation($parent, $child, $locationId)
    {
        if ($child->name === $parent->name) {
            return true;
        }
        foreach ($this->getChildrenWithLocation($child->name, $locationId) as $grandchild) {
            if ($this->detectLoopWithLocation($parent, $grandchild, $locationId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getChildren($name)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $children = $this->getChildrenWithLocation($name, $locationId);
        return $children;
    }
    
    public function getChildrenWithLocation($name, $locationId)
    {
        $query = (new Query)
            ->select(['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at', 'location_id'])
            ->from([$this->itemTable, $this->itemChildTable])
            ->andWhere(['parent' => $name,
                'name' => new Expression('[[child]]'),
                'location_id' => $locationId]);

        $children = [];
        foreach ($query->all($this->db) as $row) {
            $children[$row['name']] = $this->populateItem($row);
        }
        return $children;
    }
    
    public function add($object)
    {
        if ($object instanceof Item) {
            if ($object->ruleName && $this->getRule($object->ruleName) === null) {
                $rule = \Yii::createObject($object->ruleName);
                $rule->name = $object->ruleName;
                $this->addRule($rule);
            }

            return $this->addItem($object);
        } elseif ($object instanceof Rule) {
            return $this->addRule($object);
        }

        throw new InvalidParamException('Adding unsupported object type.');
    }
    
    public function addItem($item)
    {
        $time = time();
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'isLocationSpecific' => $item->isLocationSpecific,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
            ])->execute();

        $this->invalidateCache();

        return true;
    }
    
    public function createPermission($name)
    {
        $permission = new Permission();
        $permission->name = $name;
        return $permission;
    }
    
    public function updateItem($name, $item)
    {
        if ($item->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemChildTable, ['parent' => $item->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->itemChildTable, ['child' => $item->name], ['child' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->assignmentTable, ['item_name' => $item->name], ['item_name' => $name])
                ->execute();
        }

        $item->updatedAt = time();

        $this->db->createCommand()
            ->update($this->itemTable, [
                'name' => $item->name,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'isLocationSpecific' => $item->isLocationSpecific,
                'data' => $item->data === null ? null : serialize($item->data),
                'updated_at' => $item->updatedAt,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache();

        return true;
    }
}
