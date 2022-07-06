<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Question $question
 * @var string[]|\Cake\Collection\CollectionInterface $categories
 */
?>
<h1><?= __('Modifier la question') ?></h1>

<?= $this->Form->create($question) ?>
<fieldset>
    <?= $this->element('Questions/add_edit_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
            'action' => 'edit',
            $question->sub_category->category->checklist_id,
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
