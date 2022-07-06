<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\ChecklistFactory;
use App\Test\Factory\CustomUserFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ChecklistsController Test Case
 *
 * @uses \App\Controller\ChecklistsController
 */
class ChecklistsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test view method
     *
     * @return void
     * @uses \App\Controller\ChecklistsController::view()
     */
    public function testView(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $checklist = ChecklistFactory::make()->persist();
        $this->get('/checklists/view/' . $checklist->id);
        $this->assertResponseOk();
    }

    public function testIndex(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $this->get('/checklists/');
        $this->assertResponseOk();
    }

    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $this->get('/checklists/add/');
        $this->assertResponseOk();
    }

    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $checklist = ChecklistFactory::make()->persist();
        $this->get('/checklists/edit/' . $checklist->id);
        $this->assertResponseOk();
    }

    public function testModifiedChecklists(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $this->get('/checklists/modified-checklists');
        $this->assertResponseOk();
    }

    public function testViewModifiedChecklist(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $checklist = ChecklistFactory::make()->persist();
        $this->get('/checklists/view-modified-checklist/' . $checklist->id);
        $this->assertResponseOk();
    }
}
