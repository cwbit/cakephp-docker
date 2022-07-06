<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\CategoryFactory;
use App\Test\Factory\CustomUserFactory;
use App\Test\Factory\SubCategoryFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\SubCategoriesController Test Case
 *
 * @uses \App\Controller\SubCategoriesController
 */
class SubCategoriesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\SubCategoriesController::index()
     */
    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $category = CategoryFactory::make()->persist();
        $this->get('/sub-categories/add/' . $category->id);
        $this->assertResponseOk();
    }

    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $subCategory = SubCategoryFactory::make()->persist();
        $this->get('/sub-categories/edit/' . $subCategory->id);
        $this->assertResponseOk();
    }
}
