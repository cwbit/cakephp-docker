<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist[]|\Cake\Collection\CollectionInterface $checklists
 */
?>
<h1><?= __('Les demandes de modification des checklists') ?></h1>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('checklist_name', 'Nom') ?></th>
                <th><?= $this->Paginator->sort('machine','Machine') ?></th>
                <th><?= $this->Paginator->sort('version','Version') ?></th>
                <th><?= $this->Paginator->sort('created','Date de modification') ?></th>
                <th><?= $this->Paginator->sort('first_name','Auteur') ?></th>
                <th><?= $this->Paginator->sort('validated_at','Modification acceptée') ?></th>
                <th class="text-end"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($checklists as $checklist) : ?>
                <?php if (!$checklist->machine->is_disabled) : ?>
                    <tr>
                        <td><?= h($checklist->checklist_name) ?></td>
                        <td><?= $checklist->has('machine') ? $this->AuthLink->link($checklist->machine->machine_name, ['controller' => 'Machines', 'action' => 'view', $checklist->machine->id]) : '' ?></td>
                        <td><?= h($checklist->version) ?></td>
                        <td><?= h($checklist->created) ?></td>
                        <td><?= h($checklist->custom_user->first_name) ?></td>
                        <td>
                            <?php if ($checklist->validated_at) {
                                echo 'Approuvée';
                            } elseif ($checklist->deleted) {
                                echo 'Désapprouvée';
                            } else {
                                echo 'Pas encore inspectée';
                            }
                            ?>
                        </td>
                        <td class="text-end">
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-eye"></i>',
                                [
                                    'action' => 'viewModifiedChecklist', $checklist->id
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
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
