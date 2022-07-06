<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\CustomUserFactory;
use App\Test\Factory\MachineFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\MachinesController Test Case
 *
 * @uses \App\Controller\MachinesController
 */
class MachinesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\MachinesController::add()
     */
    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $this->get('/machines/add/');
        $this->assertResponseOk();
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\MachinesController::edit()
     */
    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $machine = MachineFactory::make()->persist();
        $this->get('/machines/edit/' . $machine->id);
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\MachinesController::view()
     */
    public function testView(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $machine = MachineFactory::make()->persist();
        $this->get('/machines/view/' . $machine->id);
        $this->assertResponseOk();
    }

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\MachinesController::index()
     */
    public function testIndex(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $this->get('/machines/');
        $this->assertResponseOk();
    }
}
