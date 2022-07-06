<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist[]|\Cake\Collection\CollectionInterface $checklists
 */
?>
<div class=" pb-2 mb-3 d-flex justify-content-between">
    <span class="text-lm fw-bold">
        <h1><?= __('Les checklists') ?></h1>
    </span>
    <?= $this->AuthLink->link(
        __('Demande de modification'),
        [
            'controller' => 'Checklists',
            'action' => 'modifiedChecklists',
        ],
        [
            'class' => 'btn btn-lg btn-secondary',
            'escape' => false
        ]
    ) ?>
</div>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('checklist_name', 'Nom') ?></th>
                <th><?= $this->Paginator->sort('Machine') ?></th>
                <th class="text-end"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($checklists as $checklist) : ?>
                <?php if (!$checklist->machine->is_disabled) : ?>
                    <tr>
                        <td><?= h($checklist->checklist_name) ?></td>
                        <td><?= $checklist->has('machine') ? $this->AuthLink->link($checklist->machine->machine_name, ['controller' => 'Machines', 'action' => 'view', $checklist->machine->id]) : '' ?></td>
                        <td class="text-end">
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-gear"></i>',
                                [
                                    'controller' => 'Controls',
                                    'action' => 'add', $checklist->id, $idUser,
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
                                    'escape' => false
                                ]
                            ) ?>
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-eye"></i>',
                                [
                                    'action' => 'view', $checklist->id
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
                                    'escape' => false
                                ]
                            ) ?>
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-pen"></i>',
                                [
                                    'action' => 'duplicate', $checklist->id
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
                                    'escape' => false
                                ]
                            ) ?>
                            <?= $this->AuthLink->postLink(
                                '<i class="bi bi-trash"></i>',
                                [
                                    'action' => 'delete', $checklist->id
                                ],
                                [
                                    'confirm' => __('Etes-vous sûr de vouloir supprimer?', $checklist->id),
                                    'class' => 'btn btn-outline-danger',
                                    'escape' => false
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator d-flex justify-content-center">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('Précédent'), array('tag' => 'li', 'class' => 'page-item', ' class' => 'page-link'), null, array('class' => 'disabled page-item', 'tag' => 'li', 'disabledTag' => 'a', ' class' => 'page-link'));
            echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'class' => 'page-item',  'currentClass' => 'disabled page-link', ' class' => 'page-link'));
            echo $this->Paginator->next(__('Suivant') . ' >', array('tag' => 'li', 'class' => 'page-item', ' class' => 'page-link'), null, array('class' => 'disabled page-item', 'tag' => 'li', 'disabledTag' => 'a', 'currentClass' => 'page-link', ' class' => 'page-link'));
            ?>
        </ul>
    </div>
</div>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Ajouter une checklist'),
        [
            'action' => 'add'
        ],
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
</div>
