<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\CustomUserFactory;
use App\Test\Factory\QuestionFactory;
use App\Test\Factory\SubCategoryFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\QuestionsController Test Case
 *
 * @uses \App\Controller\QuestionsController
 */
class QuestionsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test add method
     *
     * @return void
     * @uses \App\Controller\QuestionsController::add()
     */
    public function testAdd(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $subCategory = SubCategoryFactory::make()->persist();
        $this->get('/questions/add/' . $subCategory->id);
        $this->assertResponseOk();
    }

    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $question = QuestionFactory::make()->persist();
        $this->get('/questions/edit/' . $question->id);
        $this->assertResponseOk();
    }
}
