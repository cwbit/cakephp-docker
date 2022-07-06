<?php
declare(strict_types=1);

use Migrations\AbstractSeed;
use Cake\Utility\Text;
use \CakeDC\Users\Model\Entity\User;

/**
 * CreateUsers seed.
 */
class CreateUsersSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $users = collection([
            [
                'id' => Text::uuid(),
                'username' => 'NEI1211',
                'email' => 'christine_landanski@goodyear.com',
                'password' => (new User)->hashPassword("NEI1211"),
                'first_name' => 'Christine',
                'last_name' => 'LANDANSKI',
                'active' => 1,
                'is_superuser' => 0,
                'role' => 'supervisor',
                'tos_date' => $now,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => Text::uuid(),
                'username' => 'AA10832',
                'email' => 'vincent_merluzzi@goodyear.com',
                'password' => (new User)->hashPassword("AA10832"),
                'first_name' => 'Vincent',
                'last_name' => 'MERLUZZI',
                'active' => 1,
                'is_superuser' => 0,
                'role' => 'supervisor',
                'tos_date' => $now,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => Text::uuid(),
                'username' => 'AC33448',
                'email' => 'anthony_blondel@goodyear.com',
                'password' => (new User)->hashPassword("AC33448"),
                'first_name' => 'Anthony',
                'last_name' => 'Blondel',
                'active' => 1,
                'is_superuser' => 0,
                'role' => 'supervisor',
                'tos_date' => $now,
                'created' => $now,
                'modified' => $now,
            ],
        ])
            ->filter(function ($user) {
                return empty($this->fetchAll('SELECT * FROM users WHERE username = "' . $user['username'] . '"'));
            })
            ->toList();

        if (!empty($users)) {
            $table = $this->table('users');
            $table->insert($users)->save();
        }

    }
}
