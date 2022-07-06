<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Control $control
 */
?>
<h1 class="heading"><?= __('Contrôle Checklist') ?> <?= h($control->checklist->checklist_name) ?></h1>

<div class="text-lm text-primary fw-lighter">
    <?= __('Créée le') ?> <?= h($control->created) ?><br>
</div>
<br>
<?php if (!empty($control->checklist->categories)) : ?>

    <?php foreach ($control->checklist->categories as $category) : ?>
        <?php if ($category->security === true) : ?>
            <?php if ($category->component_code) { ?>
                <?php foreach ($control->controls_categories as $controls_category) : ?>
                    <div class="pb-2 mb-3 d-flex justify-content-between">
                        <span class="text-lm fw-bold">
                            <?= __('Code : ' . $controls_category->code_category) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php } ?>
            <div class="card mb-4" style="background-color:rgba(247, 175, 79, .2)">
                <div class="card-header d-flex justify-content-between">
                    <div class="text-lm">
                        <?= h($category->category_name) ?>
                    </div>
                </div>
                <?php foreach ($category->sub_categories as $subCategory) : ?>
                    <div class="card-body">

                        <ul class="list-group">
                            <div class="card mb-4" style="background-color:rgba(247, 175, 79, .1)">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="text-lm">
                                        <?= h($subCategory->name) ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Clé</th>
                                                    <th>Vérifications</th>
                                                    <th>Conformité</th>
                                                    <th>Valeur</th>
                                                    <th>Actions correctives</th>
                                                    <th>Statut</th>
                                                    <th class="text-end"><?= __('Actions') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($subCategory->questions as $question) : ?>
                                                    <?php foreach ($question->responses as $response) : ?>
                                                        <?php if ($response->control_id == $control->id) : ?>
                                                            <tr class="text-danger">
                                                                <td>
                                                                    <span class="<?= 'question-color question-color-' . $question->color ?>">
                                                                        <span class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                                    </span>
                                                                </td>
                                                                <td><?= h($question->entitled) ?></td>
                                                                <td><?= $response->is_conform_value ?></td>
                                                                <td>
                                                                    <?php if ($response->response_value) :
                                                                        echo $response->response_value . ' ' . $question->unity_label;
                                                                    endif;
                                                                    ?>
                                                                </td>
                                                                <td><?= $question->corrective_action; ?></td>
                                                                <td><?= $response->response_statut_value ?></td>
                                                                <td class="text-end">
                                                                    <?= $this->AuthLink->link(
                                                                        '<i class="bi bi-pen"></i>',
                                                                        [
                                                                            'controller' => 'Responses',
                                                                            'action' => 'edit', $response->id
                                                                        ],
                                                                        [
                                                                            'class' => 'btn btn-outline-primary',
                                                                            'escape' => false
                                                                        ]
                                                                    ) ?>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                    </div>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php foreach ($control->checklist->categories as $category) : ?>
        <?php if ($category->security === false) : ?>
            <?php if ($category->component_code) { ?>
                <?php foreach ($control->controls_categories as $controls_category) : ?>
                    <div class="pb-2 mb-3 d-flex justify-content-between">
                        <span class="text-lm fw-bold">
                            <?= __('Code : ' . $controls_category->code_category) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php } ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div class="text-lm">
                        <?= h($category->category_name) ?>
                    </div>
                </div>
                <?php foreach ($category->sub_categories as $subCategory) : ?>
                    <div class="card-body">

                        <ul class="list-group">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="text-lm">
                                        <?= h($subCategory->name) ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Clé</th>
                                                    <th>Vérifications</th>
                                                    <th>Conformité</th>
                                                    <th>Valeur</th>
                                                    <th>Actions correctives</th>
                                                    <th>Statut</th>
                                                    <th class="text-end"><?= __('Actions') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($subCategory->questions as $question) : ?>
                                                    <?php foreach ($question->responses as $response) : ?>
                                                        <?php if ($response->control_id == $control->id) : ?>
                                                            <tr>
                                                                <td>
                                                                    <span class="<?= 'question-color question-color-' . $question->color ?>">
                                                                        <span class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                                    </span>
                                                                </td>
                                                                <td><?= h($question->entitled) ?></td>
                                                                <td><?= $response->is_conform_value ?></td>
                                                                <td>
                                                                    <?php if ($response->response_value) :
                                                                        echo $response->response_value . ' ' . $question->unity_label;
                                                                    endif;
                                                                    ?>
                                                                </td>
                                                                <td><?= $question->corrective_action; ?></td>
                                                                <td><?= $response->response_statut_value ?></td>
                                                                <td class="text-end">
                                                                    <?= $this->AuthLink->link(
                                                                        '<i class="bi bi-pen"></i>',
                                                                        [
                                                                            'controller' => 'Responses',
                                                                            'action' => 'edit', $response->id
                                                                        ],
                                                                        [
                                                                            'class' => 'btn btn-outline-primary',
                                                                            'escape' => false
                                                                        ]
                                                                    ) ?>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                    </div>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<div class="sticky-bottom">
    <?= $this->AuthLink->link(
        __('Retour à la liste'),
        [
            'action' => 'index'
        ]
    ) ?>
    <?= $this->AuthLink->postLink(
        __('{0} Supprimer le contrôle', '<i class="bi bi-trash"></i>'),
        [
            'action' => 'delete', $control->id
        ],
        [
            'confirm' => __('Are you sure you want to delete # {0}?', $control->id), 'class' => 'side-nav-item',
            'class' => 'btn btn-outline-danger',
            'escape' => false
        ]
    ) ?>
    <?= $this->AuthLink->postlink(
        'Valider le contrôle',
        [
            'action' => 'toggleIsValid', $control->id
        ],
        [
            'confirm' => __('Etes-vous sûr de vouloir valider ce contrôle ?'),
            'class' => 'btn btn-outline-primary',
            'escape' => false
        ]
    ) ?>
</div>
