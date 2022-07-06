<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Machine $machine
 */
?>
<h1 class="heading"><?= __('Machine') ?> <?= h($machine->machine_name) ?></h1>

<div class="text-ms text-primary fw-lighter">
    <?= __('Créée le') ?> <?= h($machine->created) ?><br>
    <?= __('Modifiée le') ?> <?= h($machine->modified) ?><br>
</div>

<div class="my-4">
    <div class="mb-2">
        <span class="fw-bold">
            <?= __('Secteur :') ?>
        </span>
        <?= $machine->has('sector') ? $this->AuthLink->link($machine->sector->sector_name, ['controller' => 'Sectors', 'action' => 'view', $machine->sector->id]) : '' ?>
    </div>
    <div class="mb-2">
        <span class="fw-bold">
            <?= __('Adresse IP : ') ?>
        </span>
        <?= h($machine->ip_adress) ?>
    </div>
</div>

<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Retour à la liste'),
        [
            'action' => 'index'
        ]
    ) ?>
    <?= $this->AuthLink->link(
        __('{0} Modifier la machine', '<i class="bi bi-pen"></i>'),
        [
            'action' => 'edit', $machine->id
        ],
        [
            'class' => 'btn btn-outline-primary',
            'escape' => false
        ]
    ) ?>
    <?= $this->AuthLink->postLink(
        __('{0} Supprimer la machine', '<i class="bi bi-trash"></i>'),
        [
            'action' => 'delete', $machine->id
        ],
        [
            'confirm' => __('Are you sure you want to delete # {0}?', $machine->id), 'class' => 'side-nav-item',
            'class' => 'btn btn-outline-danger',
            'escape' => false
        ]
    ) ?>
</div>
