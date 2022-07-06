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
<h1><?= __('Ajouter une catégorie') ?></h1>

<?= $this->Form->create($category) ?>
<?= $this->Form->control('order_category', [
    'value' => $number,
    'type' => 'hidden'
]); ?>
<fieldset>
    <?= $this->element('Categories/add_edit_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
            'action' => 'edit',
            $checklistId
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
