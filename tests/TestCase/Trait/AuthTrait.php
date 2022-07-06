<?php
declare(strict_types=1);

namespace App\Test\TestCase\Trait;

use Cake\ORM\Entity;

trait AuthTrait
{
    public function loggedAs(Entity $user)
    {
        $this->session(
            [
                'Auth' => $user,
            ]
        );
    }
}
