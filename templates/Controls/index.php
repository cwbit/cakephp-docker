<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Control[]|\Cake\Collection\CollectionInterface $controls
 */
?>
<h1><?= __('Les contrôles') ?></h1>

<div class="table-responsive">
    <table class="table table-hover mx-auto">

        <thead>
            <tr class="text-center">
                <th><?= $this->Paginator->sort('created', 'Date - Heure') ?></th>
                <th><?= $this->Paginator->sort('Checklist') ?></th>
                <th><?= $this->Paginator->sort('Machine') ?></th>
                <th class="text-end"><?= __('Opérations') ?></th>
                <th class="text-end"><?= __('Validée ?') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($controls as $control) : ?>
                <?php if ($control->responses) : ?>
                    <tr class="text-center">
                        <td><?= h($control->created) ?></td>
                        <td><?= h($control->checklist->checklist_name) ?></td>
                        <td><?= h($control->checklist->machine->machine_name) ?></td>
                        <td class="text-end">
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-eye"></i>',
                                [
                                    'controller' => 'Controls',
                                    'action' => 'view', $control->id
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
                                    'escape' => false
                                ]
                            ) ?>
                        </td>
                        <td>
                            <?php if (!$control->is_valid) { ?>
                            <?php } else { ?>
                                <h3 class="bi bi-check2"></h3>
                            <?php } ?>
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
