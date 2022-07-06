<?php

/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2018, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require 'Permissions/CakeDC.php';
require 'Permissions/CustomUsers.php';
require 'Permissions/Others.php';
require 'Permissions/Sectors.php';
require 'Permissions/Machines.php';
require 'Permissions/Checklists.php';
require 'Permissions/ComponentCodeCategories.php';
require 'Permissions/Categories.php';
require 'Permissions/Questions.php';
require 'Permissions/Controls.php';
require 'Permissions/Responses.php';
require 'Permissions/SubCategories.php';
require 'Permissions/Confirms.php';
require 'Permissions/ControlsCategories.php';

use Cake\ORM\TableRegistry;

return [
    'CakeDC/Auth.permissions' =>
    array_merge(
        $permissionsCakeDC,
        $customUsers,
        $others,
        $sectors,
        $machines,
        $checklists,
        $componentCodeCategories,
        $categories,
        $questions,
        $controls,
        $responses,
        $subCategories,
        $confirms,
        $controlsCategories
    )

];
