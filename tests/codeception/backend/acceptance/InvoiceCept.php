<?php

use tests\codeception\backend\AcceptanceTester;
use tests\codeception\backend\_pages\InvoicePage;
use tests\codeception\backend\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

/* 
* Login to system
*/
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login page works');
$loginPage = LoginPage::openBy($I);

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('senguttuvang@gmail.com', 'webmaster');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see that user is logged');

$invoicePage = InvoicePage::openBy($I);

/*
* Test invoice details edit
*/
$I->amGoingTo('Test invoice details edit');
$I->click('.invoice-detail');

$I->amGoingTo('Empty date');
$invoicePage->editDetails('');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}

/*
* Test invoice message
*/
$I->amGoingTo('Add message to invoice');

$I->click('.add-invoice-note');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}

// $I->amGoingTo('submit message form with no data');
// $invoicePage->addMessage('');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }

$I->amGoingTo('try to add message with correct date');
$invoicePage->addMessage('test2 invoice message');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}

