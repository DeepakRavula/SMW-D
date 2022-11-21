<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

class m180125_075909_change_permission_name extends Migration
{
    public function up()
    {
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
        $staff = $auth->getRole(User::ROLE_STAFFMEMBER);
        $owner = $auth->getRole(User::ROLE_OWNER);
        $auth->removeAllPermissions();
        $auth->removeChildren($admin);
        $auth->removeChildren($owner);
        $auth->removeChildren($staff);
		$this->createPermissions();	
		$roles = [User::ROLE_ADMINISTRATOR, User::ROLE_STAFFMEMBER, User::ROLE_OWNER];
		$exceptStaffRoles = [User::ROLE_ADMINISTRATOR, User::ROLE_OWNER];
		$adminPermissions = $this->adminPermissions();
		$adminAndOwnerPermissions = $this->adminAndOwnerPermissions();
        $permissions = $auth->getPermissions();
        $locations = Location::find()->all();
        foreach ($locations as $location) {
            foreach ($permissions as $permission) {
				if (in_array($permission->name, $adminAndOwnerPermissions)) {
                    foreach ($exceptStaffRoles as $exceptStaffRole) {
                        $this->insert('rbac_auth_item_child', [
                            'parent' => $exceptStaffRole,
                            'child' => $permission->name,
                            'location_id' => $location->id
                        ]);
                    }
                } else if(in_array($permission->name, $adminPermissions)) {
					$this->insert('rbac_auth_item_child', [
                        'parent' => User::ROLE_ADMINISTRATOR,
                        'child' => $permission->name,
                        'location_id' => $location->id
                    ]);
				} else {
					foreach ($roles as $role) {
                        $this->insert('rbac_auth_item_child', [
                            'parent' => $role,
                            'child' => $permission->name,
                            'location_id' => $location->id
                        ]);
                    }
				}	
			}
		}
    }
	
	public function createPermissions() {
		$auth = Yii::$app->authManager;
		$manageDashboard = $auth->createPermission('manageDashboard');
        $manageDashboard->description = 'Manage dashboard';
        $auth->add($manageDashboard);
		
		$manageSchedule = $auth->createPermission('manageSchedule');
        $manageSchedule->description = 'Manage schedule';
        $auth->add($manageSchedule);
		
		$manageEnrolments = $auth->createPermission('manageEnrolments');
        $manageEnrolments->description = 'Manage enrolments';
        $auth->add($manageEnrolments);
		
		$manageStudents = $auth->createPermission('manageStudents');
        $manageStudents->description = 'Manage students';
        $auth->add($manageStudents);

		$manageCustomers = $auth->createPermission('manageCustomers');
        $manageCustomers->description = 'Manage customers';
        $auth->add($manageCustomers);
		
		$manageTeachers = $auth->createPermission('manageTeachers');
        $manageTeachers->description = 'Manage teachers';
        $auth->add($manageTeachers);
		
		$managePrivateLessons = $auth->createPermission('managePrivateLessons');
        $managePrivateLessons->description = 'Manage private lessons';
        $auth->add($managePrivateLessons);
		
		$manageGroupLessons = $auth->createPermission('manageGroupLessons');
        $manageGroupLessons->description = 'Manage group lessons';
        $auth->add($manageGroupLessons);

		$managePfi = $auth->createPermission('managePfi');
        $managePfi->description = 'Manage proforma invoices';
        $auth->add($managePfi);

		$manageInvoices = $auth->createPermission('manageInvoices');
        $manageInvoices->description = 'Manage invoices';
        $auth->add($manageInvoices);

		$manageReports = $auth->createPermission('manageReports');
        $manageReports->description = 'Manage reports';
        $auth->add($manageReports);

		$manageBirthdays = $auth->createPermission('manageBirthdays');
        $manageBirthdays->description = 'Manage birthdays';
        $auth->add($manageBirthdays);

		$managePayments = $auth->createPermission('managePayments');
        $managePayments->description = 'Manage payments';
        $auth->add($managePayments);

		$manageRoyalty = $auth->createPermission('manageRoyalty');
        $manageRoyalty->description = 'Manage royalty report';
        $auth->add($manageRoyalty);
		
		$manageTaxCollected = $auth->createPermission('manageTaxCollected');
        $manageTaxCollected->description = 'Manage tax collected report';
        $auth->add($manageTaxCollected);
		
		$manageRoyaltyFreeItems = $auth->createPermission('manageRoyaltyFreeItems');
        $manageRoyaltyFreeItems->description = 'Manage oyalty free item report';
        $auth->add($manageRoyaltyFreeItems);
		
		$manageItemsByCustomer = $auth->createPermission('manageItemsByCustomer');
        $manageItemsByCustomer->description = 'Manage items by customer report';
        $auth->add($manageItemsByCustomer);

		$manageItemReport = $auth->createPermission('manageItemReport');
        $manageItemReport->description = 'Manage item report';
        $auth->add($manageItemReport);

		$manageItemCategoryReport = $auth->createPermission('manageItemCategoryReport');
        $manageItemCategoryReport->description = 'Manage item category Report';
        $auth->add($manageItemCategoryReport);

		$manageDiscountReport = $auth->createPermission('manageDiscountReport');
        $manageDiscountReport->description = 'Manage discount report';
        $auth->add($manageDiscountReport);

		$manageAllLocations = $auth->createPermission('manageAllLocations');
        $manageAllLocations->description = 'Manage all locations report';
        $auth->add($manageAllLocations);

		$manageItems = $auth->createPermission('manageItems');
        $manageItems->description = 'Manage items';
        $auth->add($manageItems);

		$manageAdminArea = $auth->createPermission('manageAdminArea');
        $manageAdminArea->description = 'Manage admin area';
        $auth->add($manageAdminArea);

		$manageAdmin = $auth->createPermission('manageAdmin');
        $manageAdmin->description = 'Manage administrators';
        $auth->add($manageAdmin);

		$managePrograms = $auth->createPermission('managePrograms');
        $managePrograms->description = 'Manage programs';
        $auth->add($managePrograms);

		$managePrivileges = $auth->createPermission('managePrivileges');
        $managePrivileges->description = 'Manage privileges';
        $auth->add($managePrivileges);

		$manageCities = $auth->createPermission('manageCities');
        $manageCities->description = 'Manage cities';
        $auth->add($manageCities);

		$manageProvinces = $auth->createPermission('manageProvinces');
        $manageProvinces->description = 'Manage provinces';
        $auth->add($manageProvinces);

		$manageCountries = $auth->createPermission('manageCountries');
        $manageCountries->description = 'Manage countries';
        $auth->add($manageCountries);

		$manageTaxes = $auth->createPermission('manageTaxes');
        $manageTaxes->description = 'Manage taxes';
        $auth->add($manageTaxes);

		$manageReminderNotes = $auth->createPermission('manageReminderNotes');
        $manageReminderNotes->description = 'Manage reminder notes';
        $auth->add($manageReminderNotes);

		$manageColorCode = $auth->createPermission('manageColorCode');
        $manageColorCode->description = 'Manage color code';
        $auth->add($manageColorCode);

		$manageItemCategory = $auth->createPermission('manageItemCategory');
        $manageItemCategory->description = 'Manage item category';
        $auth->add($manageItemCategory);

		$manageBlogs = $auth->createPermission('manageBlogs');
        $manageBlogs->description = 'Manage blogs';
        $auth->add($manageBlogs);

		$viewBlogList = $auth->createPermission('viewBlogList');
        $viewBlogList->description = 'View blog list';
        $auth->add($viewBlogList);
		
		$manageLocations = $auth->createPermission('manageLocations');
        $manageLocations->description = 'Manage locations';
        $auth->add($manageLocations);

		$manageHolidays = $auth->createPermission('manageHolidays');
        $manageHolidays->description = 'Manage holidays';
        $auth->add($manageHolidays);

		$manageEmailTemplate = $auth->createPermission('manageEmailTemplate');
        $manageEmailTemplate->description = 'Manage email templates';
        $auth->add($manageEmailTemplate);

		$manageSetupArea = $auth->createPermission('manageSetupArea');
        $manageSetupArea->description = 'Manage setup area';
        $auth->add($manageSetupArea);

		$manageStaff = $auth->createPermission('manageStaff');
        $manageStaff->description = 'Manage staffmembers';
        $auth->add($manageStaff);

		$manageOwners = $auth->createPermission('manageOwners');
        $manageOwners->description = 'Manage owners';
        $auth->add($manageOwners);

		$manageClassrooms = $auth->createPermission('manageClassrooms');
        $manageClassrooms->description = 'Manage classrooms';
        $auth->add($manageClassrooms);

		$manageImport = $auth->createPermission('manageImport');
        $manageImport->description = 'Manage import';
        $auth->add($manageImport);

		$manageAccessControl = $auth->createPermission('manageAccessControl');
        $manageAccessControl->description = 'Manage access control';
        $auth->add($manageAccessControl);

		$manageReleaseNotes = $auth->createPermission('manageReleaseNotes');
        $manageReleaseNotes->description = 'Manage release notes';
        $auth->add($manageReleaseNotes);

		$loginToBackend = $auth->createPermission('loginToBackend');
        $loginToBackend->description = 'Login to backend';
        $auth->add($loginToBackend);

		$teacherQualificationRate = $auth->createPermission('teacherQualificationRate');
        $teacherQualificationRate->description = 'View teacher\'s qualification rate';
        $auth->add($teacherQualificationRate);
	}
	public function adminPermissions() {
		return [
			'manageAdminArea',
			'manageAdmin',
			'managePrograms',
			'managePrivileges',
			'manageCities',
			'manageProvinces',
			'manageCountries',
			'manageTaxes',
			'manageReminderNotes',
			'manageColorCode',
			'manageItemCategory',
			'manageBlogs',
			'manageLocations',
			'manageHolidays',
			'manageEmailTemplate',
			'manageOwners',
			'manageAccessControl'	
		];
	}
	public function adminAndOwnerPermissions() {
		return [
			'teacherQualificationRate',
			'manageDashboard',
			'manageBirthdays',
			'managePayments',
			'manageRoyalty',
			'manageReports',
			'manageTaxCollected',
			'manageRoyaltyFreeItems',
			'manageItemsByCustomer',
			'manageItemReport',
			'manageItemCategoryReport',
			'manageDiscountReport',
			'manageAllLocations',
			'manageStaff',
			
		];
	}
    public function down()
    {
        echo "m180125_075909_change_permission_name cannot be reverted.\n";

        return false;
    }
}
