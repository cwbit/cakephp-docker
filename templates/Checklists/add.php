<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist $checklist
 * @var \Cake\Collection\CollectionInterface|string[] $machines
 */
?>
<h1><?= __('Ajouter une checklist') ?></h1>

<?= $this->Form->create($checklist) ?>
<fieldset>
    <?= $this->element('Checklists/add_edit_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'action' => 'index'
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
