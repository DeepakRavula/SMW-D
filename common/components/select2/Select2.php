<?php
namespace common\components\select2;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Select2 extends \kartik\select2\Select2
{
    public function renderWidget()
    {
        $this->initI18N(__DIR__);
        $this->pluginOptions['theme'] = $this->theme;
        $multiple = ArrayHelper::getValue($this->pluginOptions, 'multiple', false);
        unset($this->pluginOptions['multiple']);
        $multiple = ArrayHelper::getValue($this->options, 'multiple', $multiple);
        $this->options['multiple'] = $multiple;
        if (!empty($this->addon) || empty($this->pluginOptions['width'])) {
            $this->pluginOptions['width'] = '100%';
        }
        if ($this->hideSearch) {
            $this->pluginOptions['minimumResultsForSearch'] = new JsExpression('Infinity');
        }
        $this->initPlaceholder();
        if (!isset($this->data)) {
            if (!isset($this->value) && !isset($this->initValueText)) {
                $this->data = [];
            } else {
                if ($multiple) {
                    $key = isset($this->value) && is_array($this->value) ? $this->value : [];
                } else {
                    $key = isset($this->value) ? $this->value : '';
                }
                $val = isset($this->initValueText) ? $this->initValueText : $key;
                $this->data = $multiple ? array_combine($key, $val) : [$key => $val];
            }
        }
        if (!empty($this->options['createNew'])) {
            $this->data = array_merge([0 => 'Create new'], $this->data);
        }
        Html::addCssClass($this->options, 'form-control');
        $this->initLanguage('language', true);
        $this->renderToggleAll();
        $this->registerAssets();
        $this->renderInput();
    }
}