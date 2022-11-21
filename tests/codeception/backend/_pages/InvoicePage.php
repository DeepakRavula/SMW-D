<?php

namespace tests\codeception\backend\_pages;

use yii\codeception\BasePage;

/**
 * Represents loging page.
 */
class InvoicePage extends BasePage
{
    public $route = 'invoice/view?id=4383';

    public function addMessage($content)
    {
        $this->actor->fillField('#invoice-notes', $content);
        $this->actor->click('invoice-message-button');
    }

    public function editDetails($date)
    {
        $this->actor->fillField('#invoice-date', $date);
        $this->actor->click('invoice-detail-button');
    }
}
