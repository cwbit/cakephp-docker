<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Control $control
 */
?>
<div class="users form">
    <?= $this->Form->create($operator) ?>
    <fieldset>
        <legend><?= __('Effectuer un contrôle') ?></legend>
        <?= $this->Form->control('userId', [
            'label' => 'Identifiant utilisateur',
        ]); ?>

        <?= $this->Form->control('code_categories', [
            'label' => 'Code en cours',
        ]); ?>
    </fieldset>
    <?= $this->Form->button(
        __('Valider'),
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
</div>
