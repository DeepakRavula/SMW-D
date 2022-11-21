<?php
/**
 * Eugine Terentev <eugine@terentev.net>.
 */

namespace common\rbac\rule;

use yii\rbac\Item;
use yii\rbac\Rule;

class UpdateOwnProfileRule extends Rule
{
    /** @var string */
    public $name = 'updateOwnProfileRule';

    /**
     * @param int   $user
     * @param Item  $item
     * @param array $params
     *                      - model: model to check owner
     *                      - attribute: attribute that will be compared to user ID
     *
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        return isset($params['model']) ? $params['model']->id == $user : false;
    }
}
