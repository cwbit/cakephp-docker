<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist $checklist
 */
?>
<div class="row">
    <div class="col-6 card">
        <div class="card-header">
            <?= h('Nouvelle version') ?>
        </div>

        <?= $this->element(
            'Checklists/view_modified_checklist',
            [
                'checklist' => $checklist,
                'colors' => $colors,
                'keyCodes' => $keyCodes,
                'isActiveCategory' => $isActiveCategory,
                'isActiveQuestion' => $isActiveQuestion
            ]
        ) ?>


    </div>
    <div class="col-6 card">
        <div class="card-header">
            <?= h('Anciennes versions') ?>
        </div>
        <div class="my-4">
            <?php foreach ($checklists as $key => $checklist) : ?>
                <?php if (!empty($checklist)) : ?>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#<?= 'checklist' . $key ?>" aria-expanded="false" aria-controls="<?= 'checklist' . $key ?>">
                        <?= 'Version ' . $checklist['version'] ?>
                    </button>
                    <div class="collapse" id="<?= 'checklist' . $key ?>">

                        <?= $this->element(
                            'Checklists/view_modified_checklist',
                            [
                                'checklist' => $checklist,
                                'keyCodes' => $keyCodes,
                                'isActiveCategory' => $isActiveCategory,
                                'isActiveQuestion' => $isActiveQuestion
                            ]
                        ) ?>


                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Retour à la liste'),
        [
            'action' => 'index'
        ]
    ) ?>
    <?= $this->AuthLink->postLink(
        __('{0} Désapprouver', '<i class="bi bi-trash"></i>'),
        [
            'action' => 'validate',
        ],
        [
            'confirm' => __('Êtes-vous sûr de ne pas vouloir valider la checklist # {0}?', $id), 'class' => 'side-nav-item',
            'class' => 'btn btn-outline-danger',
            'escape' => false,
            'data' => [
                'isValid' => false,
                'id' => $id
            ]
        ]
    ) ?>
    <?= $this->AuthLink->postLink(
        __('{0} Approuver', '<i class="bi bi-check"></i>'),
        [
            'action' => 'validate',
        ],
        [
            'confirm' => __('Êtes-vous sûr de vouloir valider la checklist # {0}?', $id), 'class' => 'side-nav-item',
            'class' => 'btn btn-secondary',
            'escape' => false,
            'data' => [
                'isValid' => true,
                'id' => $id
            ]
        ]
    ) ?>
</div>
