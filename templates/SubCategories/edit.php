<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SubCategory $subCategory
 * @var string[]|\Cake\Collection\CollectionInterface $categories
 */
?>

<h1><?= __('Modifier la sous-catégorie') ?></h1>

<?= $this->Form->create($subCategory) ?>
<fieldset>
    <?= $this->element('SubCategories/add_edit_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
            'action' => 'edit',
            $subCategory->category->checklist_id,
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
