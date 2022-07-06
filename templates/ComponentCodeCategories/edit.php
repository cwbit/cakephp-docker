<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ComponentCodeCategory $componentCodeCategory
 * @var string[]|\Cake\Collection\CollectionInterface $categories
 */
?>
<h1><?= __('Ajouter un code composant') ?></h1>

<?= $this->Form->create($componentCodeCategory) ?>
<fieldset>
    <?php
    echo $this->Form->control('component_code');
    ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'controller' => 'Categories',
            'action' => 'edit',
            $componentCodeCategory->category_id
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
