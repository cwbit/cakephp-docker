<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\ChecklistFactory;
use App\Test\Factory\ControlFactory;
use App\Test\Factory\CustomUserFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ControlsController Test Case
 *
 * @uses \App\Controller\ControlsController
 */
class ControlsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\ControlsController::add()
     */
    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $checklist = ChecklistFactory::make()->persist();
        $this->get('/controls/add/' . $checklist->id . '/' . 'test/' . '123');
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\ControlsController::view()
     */
    public function testView(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $control = ControlFactory::make()->persist();
        $this->get('/controls/view/' . $control->id);
        $this->assertResponseOk();
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\ControlsController::index()
     */
    public function testIndex(): void
    {
        $this->loggedAs(CustomUserFactory::make()->persist());
        $this->get('/controls/');
        $this->assertResponseOk();
    }
}
