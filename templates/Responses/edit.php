<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Response $response
 */
?>
<h1><?= __('Modifier la réponse') ?></h1>

<?= $this->Form->create($response) ?>
<fieldset>
    <?= $this->element('Responses/edit_form_fields') ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'controls',
            'action' => 'view',
            $response->control_id

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
