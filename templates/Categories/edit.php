<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Category $category
 * @var \Cake\Collection\CollectionInterface|string[] $checklists
 */
?>

<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist $checklist
 * @var \Cake\Collection\CollectionInterface|string[] $machines
 */
?>
<h1><?= __('Modifier la catégorie') ?></h1>

<?= $this->Form->create($category) ?>
<fieldset>
    <?= $this->element('Categories/add_edit_form_fields') ?>

    <table class="table table-hover">
        <thead>
            <tr class="">
                <th>Code composant</th>
                <th class="text-end"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($category->component_code_categories as $componentCode) : ?>
                <tr>
                    <td><?= $componentCode->component_code ?></td>
                    <td class="text-end">
                        <?= $this->AuthLink->link(
                            '<i class="bi bi-pen"></i>',
                            [
                                'controller' => 'ComponentCodeCategories',
                                'action' => 'edit', $componentCode->id
                            ],
                            [
                                'class' => 'btn btn-outline-primary',
                                'escape' => false
                            ]
                        ) ?>
                        <?= $this->AuthLink->postLink(
                            '<i class="bi bi-trash"></i>',
                            [
                                'controller' => 'ComponentCodeCategories',
                                'action' => 'delete', $componentCode->id
                            ],
                            [
                                'confirm' => __('Are you sure you want to delete # {0}?', $componentCode->id),
                                'class' => 'btn btn-outline-danger',
                                'escape' => false
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
            'action' => 'edit',
            $category->checklist_id,
        ]
    ) ?>
    <?= $this->Form->button(
        __('Valider'),
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
</div>
<?= $this->Form->end() ?>
