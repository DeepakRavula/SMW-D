<?php

use yii\db\Migration;
use common\models\User;

class m171218_092129_permission extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $staff = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $owner = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $auth->removeAllPermissions();
        $auth->removeChildren($admin);
        $auth->removeChildren($owner);
        $auth->removeChildren($staff);
        
        $loginToBackend = $auth->createPermission('loginToBackend');
        $loginToBackend->description = 'Can login to backend';
        $auth->add($loginToBackend);
        $auth->addChild($admin, $loginToBackend);
        
        $listCustomer = $auth->createPermission('listCustomer');
        $listCustomer->description = 'Can view the list of customer';
        $auth->add($listCustomer);
            
        $listEnrolment = $auth->createPermission('listEnrolment');
        $listEnrolment->description = 'Can view the list of enrolment';
        $auth->add($listEnrolment);

        $listGroupLesson = $auth->createPermission('listGroupLesson');
        $listGroupLesson->description = 'Can view the list of group lessons';
        $auth->add($listGroupLesson);

        $listInvoice = $auth->createPermission('listInvoice');
        $listInvoice->description = 'Can view the list of invoices';
        $auth->add($listInvoice);

        $listItem = $auth->createPermission('listItem');
        $listItem->description = 'Can view list of items';
        $auth->add($listItem);

        $listOwner = $auth->createPermission('listOwner');
        $listOwner->description = 'Can view the list of owners';
        $auth->add($listOwner);

        $listProformaInvoice = $auth->createPermission('listPfiInvoice');
        $listProformaInvoice->description = 'Can view the list of proforma invoices';
        $auth->add($listProformaInvoice);

        $listPrivateLesson = $auth->createPermission('listPrivateLesson');
        $listPrivateLesson->description = 'Can view the list of private lessons';
        $auth->add($listPrivateLesson);

        $listReleaseNote = $auth->createPermission('listReleaseNote');
        $listReleaseNote->description = 'Can view list of release notes';
        $auth->add($listReleaseNote);
        
        $listStudent = $auth->createPermission('listStudent');
        $listStudent->description = 'Can view the list of students';
        $auth->add($listStudent);

        $listTeacher = $auth->createPermission('listTeacher');
        $listTeacher->description = 'Can view the list of teachers';
        $auth->add($listTeacher);

        $viewAdmin = $auth->createPermission('viewAdmin');
        $viewAdmin->description = 'Can view admin area';
        $auth->add($viewAdmin);
        $auth->addChild($admin, $viewAdmin);
        
        $viewDashboard = $auth->createPermission('viewDashboard');
        $viewDashboard->description = 'Can view dashboard';
        $auth->add($viewDashboard);

        $viewQualificationRate = $auth->createPermission('viewQualificationRate');
        $viewQualificationRate->description = 'Can view teacher\'s qualification rate';
        $auth->add($viewQualificationRate);

        $viewReport = $auth->createPermission('viewReport');
        $viewReport->description = 'Can view reports';
        $auth->add($viewReport);

        $viewSchedule = $auth->createPermission('viewSchedule');
        $viewSchedule->description = 'Can view schedule';
        $auth->add($viewSchedule);

        $viewSetup = $auth->createPermission('viewSetup');
        $viewSetup->description = 'Can view setup area';
        $auth->add($viewSetup);
    }

    public function down()
    {
        echo "m171218_092129_permission cannot be reverted.\n";

        return false;
    }
}
