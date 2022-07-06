<?php
declare(strict_types=1);

namespace App\Model\Entity;

use CakeDC\Users\Model\Entity\User;

/**
 * Application specific User Entity with non plugin conform field(s)
 */
class CustomUser extends User
{
    public const ROLE_ADMIN = 'admin';
}
