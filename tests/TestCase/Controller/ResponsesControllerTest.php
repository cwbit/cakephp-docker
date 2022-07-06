<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Factory\CustomUserFactory;
use App\Test\Factory\ResponseFactory;
use App\Test\TestCase\Trait\AuthTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ResponsesController Test Case
 *
 * @uses \App\Controller\ResponsesController
 */
class ResponsesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AuthTrait;

    /**
     * Test edit method
     *
     * @return void
     * @uses \App\Controller\ResponsesController::edit()
     */
    public function testEdit(): void
    {
        $this->loggedAs(CustomUserFactory::make()->getEntity());
        $response = ResponseFactory::make()->persist();
        $this->get('/responses/edit/' . $response->id);
        $this->assertResponseOk();
    }
}
