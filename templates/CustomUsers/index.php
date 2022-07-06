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
    <h1 class="heading"><?= __('Les utilisateurs') ?></h1>
</div>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('username', __('Identifiant')) ?></th>
                <th><?= $this->Paginator->sort('email', __('Email')) ?></th>
                <th><?= $this->Paginator->sort('first_name', __('Prénom')) ?></th>
                <th><?= $this->Paginator->sort('last_name', __('Nom')) ?></th>
                <th><?= $this->Paginator->sort('role', __('Rôle')) ?></th>
                <th class="text-end"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?= h($user->username) ?></td>
                <td><?= h($user->email) ?></td>
                <td><?= h($user->first_name) ?></td>
                <td><?= h($user->last_name) ?></td>
                <td><?= h($user->role) ?></td>
                <td class="text-end">
                    <?= $this->AuthLink->link(
                            '<i class="bi bi-eye"></i>',
                            [
                                'controller' => 'CustomUsers',
                                'action' => 'view', 
                                $user->id
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                    ) ?>
                    <?= $this->AuthLink->link(
                            '<i class="bi bi-pen"></i>',
                            [
                                'controller' => 'CustomUsers',
                                'action' => 'edit',
                                $user->id,
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                    ) ?>
                    <?= $this->AuthLink->postLink(
                            '<i class="bi bi-trash"></i>',
                            [
                                'controller' => 'CustomUsers',
                                'action' => 'delete', 
                                $user->id
                            ],
                            [
                                'confirm' => __('Etes-vous sûr de vouloir supprimer?', $user->id),
                                'class' => 'btn btn-outline-danger',
                                'escape' => false
                            ]
                    ) ?>
                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="sticky-bottom">
        <?= $this->AuthLink->link(
            __('Ajouter un utilisateur'),
            [
                'controller' => 'CustomUsers',
                'action' => 'createUser',
                'plugin' => false,
                'prefix' => false,
            ],
            [
                'class' => 'btn btn-lg btn-secondary'
            ]
        ) ?>
    </div>
</div>
