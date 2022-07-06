<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ComponentCodeCategory $componentCodeCategory
 * @var \Cake\Collection\CollectionInterface|string[] $categories
 */
?>
<h1><?= __('Ajouter un code composant') ?></h1>

<?= $this->Form->create($componentCodeCategory) ?>
<fieldset>
    <?php
    echo $this->Form->control(
        'category_id',
        [
            'value' => $category->id,
            'type' => 'hidden'
        ]
    );
    echo $this->Form->control('component_code');
    ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Checklists',
            'action' => 'edit',
            $category->checklist_id
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
