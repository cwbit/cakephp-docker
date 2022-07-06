<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Control $control
 */
?>
<h1><?= __('Effectuer un contrôle') ?></h1>
<?= $this->Form->create($control) ?>
<fieldset>
    <?= $this->element('Controls/add_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
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
