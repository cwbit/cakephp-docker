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

?>
<div class="actions columns large-2 medium-3">
    <h1 class="heading"><?= __('Utilisateur') ?> <?= h($user->last_name) ?></h1><br>
</div>
<div class="users view large-10 medium-9 columns">
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Identifiant') ?></h6>
            <p><?= h($user->username) ?></p>
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Email') ?></h6>
            <p><?= h($user->email) ?></p>
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Prénom') ?></h6>
            <p><?= h($user->first_name) ?></p>
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Nom') ?></h6>
            <p><?= h($user->last_name) ?></p>
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Rôle') ?></h6>
            <p><?= h($user->role) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Created') ?></h6>
            <p><?= h($user->created) ?></p>
            <h6 class="subheader"><?= __d('cake_d_c/users', 'Modified') ?></h6>
            <p><?= h($user->modified) ?></p>
        </div>
    </div>
</div>
