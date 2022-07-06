<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sector $sector
 */
?>
<h1 class="heading"><?= __('Secteur') ?> <?= h($sector->sector_name) ?></h1>

<div class="text-ms text-primary fw-lighter">
    <?= __('Créée le') ?> <?= h($sector->created) ?><br>
    <?= __('Modifiée le') ?> <?= h($sector->modified) ?><br>
</div>

<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Retour à la liste'),
        [
            'action' => 'index'
        ]
    ) ?>
    <?= $this->AuthLink->link(
        __('{0} Modifier le secteur', '<i class="bi bi-pen"></i>'),
        [
            'action' => 'edit', $sector->id
        ],
        [
            'class' => 'btn btn-outline-primary',
            'escape' => false
        ]
    ) ?>
    <?= $this->AuthLink->postLink(
        __('{0} Supprimer le secteur', '<i class="bi bi-trash"></i>'),
        [
            'action' => 'delete', $sector->id
        ],
        [
            'confirm' => __('Are you sure you want to delete # {0}?', $sector->id), 'class' => 'side-nav-item',
            'class' => 'btn btn-outline-danger',
            'escape' => false
        ]
    ) ?>
</div>
