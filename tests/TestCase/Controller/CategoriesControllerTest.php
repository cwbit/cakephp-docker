<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\CategoryFactory;
use App\Test\Factory\ChecklistFactory;
use App\Test\Factory\CustomUserFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\CategoriesController Test Case
 *
 * @uses \App\Controller\CategoriesController
 */
class CategoriesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\CategoriesController::add()
     */
    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $checklist = ChecklistFactory::make()->persist();
        $this->get('/categories/add/' . $checklist->id);
        $this->assertResponseOk();
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\CategoriesController::edit()
     */
    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $category = CategoryFactory::make()->persist();
        $this->get('/categories/add/' . $category->id);
        $this->assertResponseOk();
    }
}
