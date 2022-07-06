<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sector[]|\Cake\Collection\CollectionInterface $sectors
 */
?>
<h1><?= __('Les secteurs') ?></h1>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('sector_name', 'Nom du secteur') ?></th>
                <th class="text-end"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sectors as $sector) : ?>
                <tr>
                    <td><?= h($sector->sector_name) ?></td>
                    <td class="text-end">
                        <?= $this->AuthLink->link(
                            '<i class="bi bi-eye"></i>',
                            [
                                'action' => 'view', $sector->id
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                        ) ?>
                        <?= $this->AuthLink->link(
                            '<i class="bi bi-pen"></i>',
                            [
                                'action' => 'edit', $sector->id
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                        ) ?>
                        <?= $this->AuthLink->postLink(
                            '<i class="bi bi-trash"></i>',
                            [
                                'action' => 'delete', $sector->id
                            ],
                            [
                                'confirm' => __('Etes-vous sûr de vouloir supprimer?', $sector->id),
                                'class' => 'btn btn-outline-danger',
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
        __('Ajouter un secteur'),
        [
            'action' => 'add'
        ],
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
</div>
