<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Question $question
 * @var \Cake\Collection\CollectionInterface|string[] $categories
 */
?>
<h1><?= __('Ajouter une question') ?></h1>

<?= $this->Form->create($question) ?>
<?= $this->Form->control('order_question', [
    'value' => $number,
    'type' => 'hidden'
]); ?>
<fieldset>
    <?= $this->element('Questions/add_edit_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
            'action' => 'edit',
            $subCategory->category->checklist_id
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
