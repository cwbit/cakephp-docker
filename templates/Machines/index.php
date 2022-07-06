<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Machine[]|\Cake\Collection\CollectionInterface $machines
 */
?>
<h1><?= __('Les machines') ?></h1>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('machine_name', 'Nom') ?></th>
                <th><?= $this->Paginator->sort('secteur') ?></th>
                <th><?= $this->Paginator->sort('adresse ip') ?></th>
                <th class="text-end"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($machines as $machine) : ?>
                <?php if ($machine->is_disabled) { ?>
                    <tr style="background-color: rgba(66, 65, 65, 0.3);">
                    <?php } else { ?>
                    <tr>
                    <?php } ?>
                    <td><?= h($machine->machine_name) ?></td>
                    <td><?= $machine->has('sector') ? $this->AuthLink->link($machine->sector->sector_name, ['controller' => 'Sectors', 'action' => 'view', $machine->sector->id]) : '' ?></td>
                    <td><?= h($machine->ip_adress) ?></td>
                    <td class="text-end">
                        <?= $this->AuthLink->link(
                            '<i class="bi bi-eye"></i>',
                            [
                                'action' => 'view', $machine->id
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                        ) ?>
                        <?= $this->AuthLink->link(
                            '<i class="bi bi-pen"></i>',
                            [
                                'action' => 'edit', $machine->id
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                        ) ?>
                        <?= $this->AuthLink->postLink(
                            'Activer/Desactiver',
                            [
                                'action' => 'toggleIsDisabled', $machine->id
                            ],
                            [
                                'confirm' => __('Etes-vous sûr de vouloir activer/desactiver cette machine ?'),
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                        ) ?>

                    </td>
                    </tr>
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
        __('Ajouter une machine'),
        [
            'action' => 'add'
        ],
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
</div>
