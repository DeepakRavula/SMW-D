<?php
namespace tests\codeception\common;
use tests\codeception\common\fixtures\ClassroomFixture;
use common\models\Classroom;
use common\models\City;
class ClassroomTest extends \Codeception\Test\Unit
{
    /**
     * @var \tests\codeception\common\UnitTester
     */
    protected $tester;

    // tests
//    public function _fixtures()
//    {
//        return [ 'classroom' => ClassroomFixture::className() ];
//    }
  public function _fixtures()
    {
        return [
            'profiles' => [
                'class' => ClassroomFixture::className(),
                // fixture data located in tests/_data/user.php
                'dataFile' => codecept_data_dir() . 'classroom.php'
            ],
        ];
    }


//    public function testClassroom(){
//        $classroom = Classroom::findOne( [ "id" => 79 ] );
//        $classroomId = isset( $classroom->id ) ? $classroom->id : false;
//        $this->assertEquals( 79, $classroomId );
//    }
//    public function _fixtures() {
//        return [
//            'classroom'   => 'tests\codeception\common\fixtures\ClassroomFixture',
//        ];
//    }
//
//    public function testClass() {
//        $classroom1 = $this->tester->grabFixture('classroom', 'classroom1');
//        $this->assertEquals(1, $classroom1->id);
//    }
//    public function testClassroomCreate()
//    {
//        $class = new Classroom();
//	$class->locationId =1;
//        $class->name = 'classroom';
//        $class->description = 'newdescription';
//        $this->assertTrue($class->save());
//        $this->assertTrue($class->name === 'classroom');
//	$this->assertTrue($class->validate(),'true true');	  
//    }
//     public function testCityCreate()
//    {
//        $city = new City();
//	$city->name = 'new';
//	$city->province_id = 1;
//        $this->assertTrue($city->save());	
//	$this->assertEquals($city->name,'new');
//    }
}