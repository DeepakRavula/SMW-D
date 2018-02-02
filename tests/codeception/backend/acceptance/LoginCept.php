<?php

use tests\codeception\backend\AcceptanceTester;
use tests\codeception\backend\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login page works');

$loginPage = LoginPage::openBy($I);

$I->amGoingTo('submit login form with no data');
$loginPage->login('', '');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see validations errors');
$I->see('Username cannot be blank.', '.help-block');
$I->see('Password cannot be blank.', '.help-block');

$I->amGoingTo('try to login with wrong credentials');
$I->expectTo('see validations errors');
$loginPage->login('admin', 'wrong');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see validations errors');
$I->see('Incorrect username or password.', '.help-block');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login('senguttuvang@gmail.com', 'webmaster');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->expectTo('see that user is logged');

$I->click('#user-menu');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->seeLink('Logout');

$I->click('Logout');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->dontSeeLink('Logout)');
$I->see('Sign me in', 'button[name="login-button"]');
