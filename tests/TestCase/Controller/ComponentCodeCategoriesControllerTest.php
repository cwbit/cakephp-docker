<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\CategoryFactory;
use App\Test\Factory\ComponentCodeCategoryFactory;
use App\Test\Factory\CustomUserFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ComponentCodeCategoriesController Test Case
 *
 * @uses \App\Controller\ComponentCodeCategoriesController
 */
class ComponentCodeCategoriesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\ComponentCodeCategoriesController::add()
     */
    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $category = CategoryFactory::make()->persist();
        $this->get('/component-code-categories/add/' . $category->id);
        $this->assertResponseOk();
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\ComponentCodeCategoriesController::edit()
     */
    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $componentCode = ComponentCodeCategoryFactory::make()->persist();
        $this->get('/component-code-categories/edit/' . $componentCode->id);
        $this->assertResponseOk();
    }
}
