<?php
namespace common\components\select2;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Select2 extends \kartik\select2\Select2
{
    public function init()
    {
        if (!empty($this->options['createNew'])) {
            $this->data = array_merge([0 => 'Create new'], $this->data);
        }
        return parent::init();
    }
}