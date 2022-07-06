<?php
declare(strict_types=1);

/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2020 Juan Pablo Ramirez and Nicolas Masson
 * @link          https://webrider.de/
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Migrations\AbstractMigration;

class CreateProductsTable extends AbstractMigration
{

    public function change(): void
    {
        $table = $this->table('products');
        $table
            ->addColumn('name', 'string')
            ->create();
    }

    public function down(): void
    {
        $this
            ->table('products')
            ->drop();
    }
}

