<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Confirm $confirm
 * @var \Cake\Collection\CollectionInterface|string[] $controls
 * @var \Cake\Collection\CollectionInterface|string[] $questions
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->AuthLink->link(__('Liste des réponses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="confirms form content">
            <?= $this->Form->create($confirm) ?>
            <fieldset>
                <legend><?= __('Ajouter confirmation') ?></legend>
                <?php
                    echo $this->Form->control('control_id', ['options' => $controls]);
                    echo $this->Form->control('question_id', ['options' => $questions]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Valider')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
