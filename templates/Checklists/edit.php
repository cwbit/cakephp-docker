<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist $checklist
 * @var string[]|\Cake\Collection\CollectionInterface $machines
 */
?>
<h1><?= __('Modifier la checklist') ?></h1>

<?= $this->Form->create($checklist) ?>
<fieldset>
    <?= $this->element('Checklists/add_edit_form_fields') ?>
    <?= $this->Form->button(
        __('Valider'),
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
    <h1 class="heading"><?= __('Checklist') ?> <?= h($checklist->checklist_name) ?></h1>

    <div class="text-ms text-primary fw-lighter">
        <?= __('Créée le') ?> <?= h($checklist->created) ?><br>
        <?= __('Modifiée le') ?> <?= h($checklist->modified) ?><br>
    </div>

    <div class="my-4">
        <div class="mb-2">
            <span class="fw-bold">
                <?= __('Machine :') ?>
            </span>
            <?= $checklist->has('machine') ? $this->AuthLink->link($checklist->machine->machine_name, ['controller' => 'Machines', 'action' => 'view', $checklist->machine->id]) : '' ?>
        </div>
    </div>

    <div class="border-bottom pb-2 mb-3 d-flex justify-content-between">
        <?= $this->AuthLink->link(
            __('Ajouter une catégorie'),
            [
                'controller' => 'Categories',
                'action' => 'add', $checklist->id
            ],
            [
                'class' => 'btn btn-secondary'
            ]
        ); ?>
    </div>

    <?php if (!empty($checklist->categories)) : ?>

        <?php foreach ($checklist->categories as $categories) : ?>
            <?php if ($categories->security === true) : ?>
                <div class="card mb-4" style="background-color:rgba(247, 175, 79, .2)">
                    <div class="card-header d-flex justify-content-between">
                        <div class="text-lm">
                            <?= h($categories->category_name) ?>
                            <?= '(' . $categories->is_active_category . ')' ?>
                            <?php if ($categories->component_code) : ?>
                                <?= '(' ?>
                                <?php foreach ($categories->component_code_categories as $componentCodeCategories) : ?>
                                    <?= $componentCodeCategories->component_code ?>
                                <?php endforeach; ?>
                                <?= ')' ?>
                                <?= $this->AuthLink->link(
                                    __('Ajouter la famille'),
                                    [
                                        'controller' => 'ComponentCodeCategories',
                                        'action' => 'add', $categories->id
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary'
                                    ]
                                ); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($countCategories <= 1) { ?>
                            <?php } elseif ($categories->order_category == null) { ?>
                            <?php } elseif ($categories->order_category == 1) { ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-down-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'down',
                                        ],
                                    ]
                                ) ?>
                            <?php } elseif ($categories->order_category == $countCategories) { ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-up-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'up',
                                        ],
                                    ]
                                ) ?>
                            <?php } else { ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-down-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'down',
                                        ],
                                    ]
                                ) ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-up-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'up',
                                        ],
                                    ]
                                ) ?>
                            <?php } ?>
                        </div>
                        <div>
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-pen"></i>',
                                [
                                    'controller' => 'Categories', 'action' => 'edit', $categories->id
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
                                    'escape' => false
                                ]
                            ) ?>
                            <?= $this->AuthLink->postLink(
                                '<i class="bi bi-trash"></i>',
                                [
                                    'controller' => 'Categories', 'action' => 'delete', $categories->id
                                ],
                                [
                                    'confirm' => __('Are you sure you want to delete # {0}?', $categories->id),
                                    'class' => 'btn btn-outline-danger',
                                    'escape' => false
                                ]
                            ) ?>
                        </div>

                    </div>
                    <div class="card-body">

                        <ul class="list-group">
                            <div class="pb-2 mb-3 d-flex justify-content-between">
                                <div class="text-end mt-2">
                                    <?= $this->AuthLink->link(
                                        'Ajouter une sous-catégorie',
                                        [
                                            'controller' => 'SubCategories', 'action' => 'add',
                                            $categories->id, 'plugin' => false
                                        ],
                                        [
                                            'class' => 'btn btn-secondary',
                                            'escape' => false
                                        ]
                                    ); ?>
                                </div>
                            </div>
                            <?php
                            foreach ($categories->sub_categories as $subCategory) : ?>
                                <div class="card mb-4" style="background-color:rgba(247, 175, 79, .1)">
                                    <div class="card-header d-flex justify-content-between">
                                        <div class="text-lm">
                                            <?= h($subCategory->name) ?>
                                        </div>
                                        <div class="">
                                            <?= $this->AuthLink->link(
                                                '<i class="bi bi-pen"></i>',
                                                [
                                                    'controller' => 'SubCategories', 'action' => 'edit',
                                                    $subCategory->id, 'plugin' => false
                                                ],
                                                [
                                                    'class' => 'btn btn-sm btn-outline-primary',
                                                    'escape' => false
                                                ]
                                            ); ?>
                                            <?= $this->AuthLink->postLink(
                                                '<i class="bi bi-trash"></i>',
                                                [
                                                    'controller' => 'SubCategories', 'action' => 'delete', $subCategory->id
                                                ],
                                                [
                                                    'confirm' => __('Are you sure you want to delete # {0}?', $subCategory->id),
                                                    'class' => 'btn btn-sm btn-outline-danger',
                                                    'escape' => false
                                                ]
                                            ) ?>
                                        </div>
                                    </div>
                                    <?php $countQuestions = count($subCategory->questions) ?>
                                    <div class="card-body">
                                        <?php foreach ($subCategory->questions as $question) : ?>
                                            <li class="list-group-item d-flex justify-content-between text-danger" style="background-color:rgba(247, 175, 79, .1)">
                                                <div class="d-flex justify-content-start">
                                                    <span class="<?= 'question-color question-color-' . $question->color ?>">
                                                        <span class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                    </span>
                                                    <?= $question->is_active_question . " : " ?>
                                                    <?= h($question->entitled) ?>
                                                </div>
                                                <div class="">
                                                    <?= $this->element(
                                                        'Questions/change_position_question',
                                                        [
                                                            'question' => $question,
                                                            'countQuestions' => $countQuestions,
                                                            'checklist' => $checklist
                                                        ]
                                                    ) ?>
                                                    <?= $this->AuthLink->link(
                                                        '<i class="bi bi-pen"></i>',
                                                        [
                                                            'controller' => 'Questions', 'action' => 'edit',
                                                            $question->id, 'plugin' => false
                                                        ],
                                                        [
                                                            'class' => 'btn btn-sm btn-outline-primary',
                                                            'escape' => false
                                                        ]
                                                    ); ?>
                                                    <?= $this->AuthLink->postLink(
                                                        '<i class="bi bi-trash"></i>',
                                                        [
                                                            'controller' => 'Questions', 'action' => 'delete', $question->id
                                                        ],
                                                        [
                                                            'confirm' => __('Êtes-vous sûr de vouloir supprimer la question # {0}?', $question->id),
                                                            'class' => 'btn btn-sm btn-outline-danger',
                                                            'escape' => false
                                                        ]
                                                    ) ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                        <div class="text-end mt-2">
                                            <?= $this->AuthLink->link(
                                                'Ajouter une question',
                                                [
                                                    'controller' => 'Questions', 'action' => 'add',
                                                    $subCategory->id, 'plugin' => false
                                                ],
                                                [
                                                    'class' => 'btn btn-secondary',
                                                    'escape' => false
                                                ]
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach ($checklist->categories as $categories) : ?>
            <?php if ($categories->security === false) : ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <div class="text-lm">
                            <?= h($categories->category_name) ?>
                            <?= '(' . $categories->is_active_category . ')' ?>
                            <?php if ($categories->component_code) : ?>
                                <?= '(' ?>
                                <?php foreach ($categories->component_code_categories as $componentCodeCategories) : ?>
                                    <?= $componentCodeCategories->component_code ?>
                                <?php endforeach; ?>
                                <?= ')' ?>
                                <?= $this->AuthLink->link(
                                    __('Ajouter la famille'),
                                    [
                                        'controller' => 'ComponentCodeCategories',
                                        'action' => 'add', $categories->id
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary'
                                    ]
                                ); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($countCategories <= 1) { ?>
                            <?php } elseif ($categories->order_category == null) { ?>
                            <?php } elseif ($categories->order_category == 1) { ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-down-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'down',
                                        ],
                                    ]
                                ) ?>
                            <?php } elseif ($categories->order_category == $countCategories) { ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-up-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'up',
                                        ],
                                    ]
                                ) ?>
                            <?php } else { ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-down-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'down',
                                        ],
                                    ]
                                ) ?>
                                <?= $this->AuthLink->postLink(
                                    '<i class="bi bi-arrow-up-short"></i>',
                                    [
                                        'controller' => 'Categories', 'action' => 'changePositionCategory'
                                    ],
                                    [
                                        'class' => 'btn btn-outline-primary',
                                        'escape' => false,
                                        'data' => [
                                            'id' => $categories->id,
                                            'direction' => 'up',
                                        ],
                                    ]
                                ) ?>
                            <?php } ?>
                        </div>
                        <div>
                            <?= $this->AuthLink->link(
                                '<i class="bi bi-pen"></i>',
                                [
                                    'controller' => 'Categories', 'action' => 'edit', $categories->id
                                ],
                                [
                                    'class' => 'btn btn-outline-primary',
                                    'escape' => false
                                ]
                            ) ?>
                            <?= $this->AuthLink->postLink(
                                '<i class="bi bi-trash"></i>',
                                [
                                    'controller' => 'Categories', 'action' => 'delete', $categories->id
                                ],
                                [
                                    'confirm' => __('Are you sure you want to delete # {0}?', $categories->id),
                                    'class' => 'btn btn-outline-danger',
                                    'escape' => false
                                ]
                            ) ?>
                        </div>

                    </div>
                    <div class="card-body">

                        <ul class="list-group">
                            <div class="pb-2 mb-3 d-flex justify-content-between">
                                <div class="text-end mt-2">
                                    <?= $this->AuthLink->link(
                                        'Ajouter une sous-catégorie',
                                        [
                                            'controller' => 'SubCategories', 'action' => 'add',
                                            $categories->id, 'plugin' => false
                                        ],
                                        [
                                            'class' => 'btn btn-secondary',
                                            'escape' => false
                                        ]
                                    ); ?>
                                </div>
                            </div>
                            <?php
                            foreach ($categories->sub_categories as $subCategory) : ?>
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between">
                                        <div class="text-lm">
                                            <?= h($subCategory->name) ?>
                                        </div>
                                        <div class="">
                                            <?= $this->AuthLink->link(
                                                '<i class="bi bi-pen"></i>',
                                                [
                                                    'controller' => 'SubCategories', 'action' => 'edit',
                                                    $subCategory->id, 'plugin' => false
                                                ],
                                                [
                                                    'class' => 'btn btn-sm btn-outline-primary',
                                                    'escape' => false
                                                ]
                                            ); ?>
                                            <?= $this->AuthLink->postLink(
                                                '<i class="bi bi-trash"></i>',
                                                [
                                                    'controller' => 'SubCategories', 'action' => 'delete', $subCategory->id
                                                ],
                                                [
                                                    'confirm' => __('Are you sure you want to delete # {0}?', $subCategory->id),
                                                    'class' => 'btn btn-sm btn-outline-danger',
                                                    'escape' => false
                                                ]
                                            ) ?>
                                        </div>
                                    </div>
                                    <?php $countQuestions = count($subCategory->questions) ?>
                                    <div class="card-body">
                                        <?php foreach ($subCategory->questions as $question) : ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <div class="d-flex justify-content-start">
                                                    <span class="<?= 'question-color question-color-' . $question->color ?>">
                                                        <span class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                    </span>
                                                    <?= $question->is_active_question . " : " ?>
                                                    <?= h($question->entitled) ?>
                                                </div>
                                                <div class="">
                                                    <?= $this->element(
                                                        'Questions/change_position_question',
                                                        [
                                                            'question' => $question,
                                                            'countQuestions' => $countQuestions,
                                                            'checklist' => $checklist
                                                        ]
                                                    ) ?>
                                                    <?= $this->AuthLink->link(
                                                        '<i class="bi bi-pen"></i>',
                                                        [
                                                            'controller' => 'Questions', 'action' => 'edit',
                                                            $question->id, 'plugin' => false
                                                        ],
                                                        [
                                                            'class' => 'btn btn-sm btn-outline-primary',
                                                            'escape' => false
                                                        ]
                                                    ); ?>
                                                    <?= $this->AuthLink->postLink(
                                                        '<i class="bi bi-trash"></i>',
                                                        [
                                                            'controller' => 'Questions', 'action' => 'delete', $question->id
                                                        ],
                                                        [
                                                            'confirm' => __('Êtes-vous sûr de vouloir supprimer la question # {0}?', $question->id),
                                                            'class' => 'btn btn-sm btn-outline-danger',
                                                            'escape' => false
                                                        ]
                                                    ) ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                        <div class="text-end mt-2">
                                            <?= $this->AuthLink->link(
                                                'Ajouter une question',
                                                [
                                                    'controller' => 'Questions', 'action' => 'add',
                                                    $subCategory->id, 'plugin' => false
                                                ],
                                                [
                                                    'class' => 'btn btn-secondary',
                                                    'escape' => false
                                                ]
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php endif; ?>
</fieldset>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Annuler'),
        [
            'action' => 'index'
        ]
    ) ?>
    <?= $this->AuthLink->link(
        __('Valider'),
        [
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-lg btn-secondary'
        ]
    ) ?>
</div>
<?= $this->Form->end() ?>
