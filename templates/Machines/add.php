<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Machine $machine
 * @var \Cake\Collection\CollectionInterface|string[] $sectors
 */
?>
<h1><?= __('Ajouter une machine') ?></h1>

<?= $this->Form->create($machine) ?>
<fieldset>
    <?= $this->element('Machines/add_edit_form_fields') ?>
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
