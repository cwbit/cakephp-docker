<h1 class="heading"><?= __('Checklist') ?> <?= h($checklist->checklist_name) ?></h1>

<div class="text-ms text-primary fw-lighter">
    <?= __('Créée le') ?> <?= h($checklist->created) ?><br>
    <?= __('Modifiée le') ?> <?= h($checklist->modified) ?><br>
</div>
<br>
<?= $this->Form->control("is_valid", [
    'type' => 'hidden',
    'value' => 0
]); ?>
<?php if (!empty($checklist->categories)) : ?>
    <div class="pb-2 mb-3 d-flex justify-content-between">
        <span class="text-lm fw-bold">
            <?= $this->Form->control("controls_categories.code_category", [
                'label' => 'Code : ',
                'value' => $codeCategory,
                'type' => 'hidden'
            ]); ?>
        </span>
    </div>
    <?php foreach ($checklist->categories as $key => $category) : ?>
        <?php if (!$category->component_code) { ?>
            <?php if (!$category->is_disabled) : ?>
                <?php if ($category->security === true) : ?>
                    <div class="card mb-4" style="background-color:rgba(247, 175, 79, .2)">
                        <div class="card-header d-flex justify-content-between">
                            <div class="text-lm">
                                <?= h($category->category_name) ?>
                            </div>
                            <?php if ($category->column_na) : ?>
                                <?= $this->Form->button(
                                    'Vérifications non applicables',
                                    [
                                        'type' => 'button',
                                        'class' => 'buttonsQuestionsNa'
                                    ]
                                ); ?>
                            <?php endif; ?>
                        </div>

                        <?php foreach ($category->sub_categories as $subCategory) : ?>
                            <div class="card-body">

                                <ul class="list-group">
                                    <div class="card mb-4" style="background-color:rgba(247, 175, 79, .1)">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="text-lm">
                                                <?= h($subCategory->name) ?>
                                            </div>
                                            <?php if ($subCategory->column_na) : ?>
                                                <?= $this->Form->button(
                                                    'Vérifications non applicables',
                                                    [
                                                        'type' => 'button',
                                                        'class' => 'buttonsQuestionsNa'
                                                    ]
                                                ); ?>
                                            <?php endif; ?>
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
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($subCategory->questions as $question) : ?>
                                                        <?php if (!$question->is_disabled) : ?>
                                                            <?= $this->Form->control("responses.$question->id.question_id", [
                                                                'type' => 'hidden',
                                                                'value' => $question->id
                                                            ]); ?>
                                                            <?= $this->Form->control("responses.$question->id.response_statut", [
                                                                'type' => 'hidden',
                                                                'value' => 1
                                                            ]); ?>
                                                            <tr>
                                                                <td>
                                                                        <span
                                                                            class="<?= 'question-color question-color-' . $question->color ?>">
                                                                            <span
                                                                                class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                                        </span>
                                                                </td>
                                                                <td class="text-danger"><?= h($question->entitled) ?></td>
                                                                <td class="conformity">
                                                                    <?php if ($question->column_na) { ?>
                                                                        <?= $this->Form->radio(
                                                                            "responses.$question->id.is_conform",
                                                                            $isConformWithNa,
                                                                            [
                                                                                'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                            ]
                                                                        ); ?>
                                                                    <?php } else { ?>
                                                                        <?= $this->Form->radio(
                                                                            "responses.$question->id.is_conform",
                                                                            $isConform,
                                                                            [
                                                                                'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                            ]
                                                                        ); ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <?php if ($question->is_value_required) { ?>
                                                                    <td><?= $this->Form->control("responses.$question->id.response_value", [
                                                                            'label' => false,
                                                                            'placeholder' => 'Valeur',
                                                                            'append' => $question->unity_label,
                                                                        ]); ?></td>
                                                                <?php } else { ?>
                                                                    <td style="background-color:rgba(66, 65, 65, .4)"></td>
                                                                <?php } ?>
                                                                <td class="text-danger">
                                                                    <p id="<?= 'responses-' . $question->id . '-is-conform' ?>"
                                                                       style="display:none"><?= $question->corrective_action; ?></p>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        id="<?= 'responses-' . $question->id . '-response-status' ?>"
                                                                        style="display:none">
                                                                        <?= $this->Form->radio(
                                                                            "responses.$question->id.response_statut",
                                                                            $isValid,
                                                                        ); ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
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
            <?php endif; ?>
        <?php } else { ?>
            <?php $test = false; ?>
            <?php foreach ($category->component_code_categories as $component_code_category) :
                if ($component_code_category->component_code == $codeComponent || $codeComponent == '') :
                    $test = true;
                endif;
            endforeach; ?>
            <?php if ($test) : ?>
                <?php if (!$category->is_disabled) : ?>
                    <?php if ($category->security === true) : ?>
                        <div class="card mb-4" style="background-color:rgba(247, 175, 79, .2)">
                            <div class="card-header d-flex justify-content-between">
                                <div class="text-lm">
                                    <?= h($category->category_name) ?>
                                </div>
                                <?php if ($category->column_na) : ?>
                                    <?= $this->Form->button(
                                        'Vérifications non applicables',
                                        [
                                            'type' => 'button',
                                            'class' => 'buttonsQuestionsNa'
                                        ]
                                    ); ?>
                                <?php endif; ?>
                            </div>

                            <?php foreach ($category->sub_categories as $subCategory) : ?>
                                <div class="card-body">

                                    <ul class="list-group">
                                        <div class="card mb-4" style="background-color:rgba(247, 175, 79, .1)">
                                            <div class="card-header d-flex justify-content-between">
                                                <div class="text-lm">
                                                    <?= h($subCategory->name) ?>
                                                </div>
                                                <?php if ($subCategory->column_na) : ?>
                                                    <?= $this->Form->button(
                                                        'Vérifications non applicables',
                                                        [
                                                            'type' => 'button',
                                                            'class' => 'buttonsQuestionsNa'
                                                        ]
                                                    ); ?>
                                                <?php endif; ?>
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
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($subCategory->questions as $question) : ?>
                                                            <?php if (!$question->is_disabled) : ?>
                                                                <?= $this->Form->control("responses.$question->id.question_id", [
                                                                    'type' => 'hidden',
                                                                    'value' => $question->id
                                                                ]); ?>
                                                                <?= $this->Form->control("responses.$question->id.response_statut", [
                                                                    'type' => 'hidden',
                                                                    'value' => 1
                                                                ]); ?>
                                                                <tr>
                                                                    <td>
                                                                                <span
                                                                                    class="<?= 'question-color question-color-' . $question->color ?>">
                                                                                    <span
                                                                                        class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                                                </span>
                                                                    </td>
                                                                    <td class="text-danger"><?= h($question->entitled) ?></td>
                                                                    <td class="conformity">
                                                                        <?php if ($question->column_na) { ?>
                                                                            <?= $this->Form->radio(
                                                                                "responses.$question->id.is_conform",
                                                                                $isConformWithNa,
                                                                                [
                                                                                    'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                                ]
                                                                            ); ?>
                                                                        <?php } else { ?>
                                                                            <?= $this->Form->radio(
                                                                                "responses.$question->id.is_conform",
                                                                                $isConform,
                                                                                [
                                                                                    'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                                ]
                                                                            ); ?>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <?php if ($question->is_value_required) { ?>
                                                                        <td><?= $this->Form->control("responses.$question->id.response_value", [
                                                                                'label' => false,
                                                                                'placeholder' => 'Valeur',
                                                                                'append' => $question->unity_label,
                                                                            ]); ?></td>
                                                                    <?php } else { ?>
                                                                        <td style="background-color:rgba(66, 65, 65, .4)"></td>
                                                                    <?php } ?>
                                                                    <td class="text-danger">
                                                                        <p id="<?= 'responses-' . $question->id . '-is-conform' ?>"
                                                                           style="display:none"><?= $question->corrective_action; ?></p>
                                                                    </td>
                                                                    <td>
                                                                        <div
                                                                            id="<?= 'responses-' . $question->id . '-response-status' ?>"
                                                                            style="display:none">
                                                                            <?= $this->Form->radio(
                                                                                "responses.$question->id.response_statut",
                                                                                $isValid,
                                                                            ); ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
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
                <?php endif; ?>
            <?php endif; ?>
        <?php } ?>
    <?php endforeach; ?>
    <?php foreach ($checklist->categories as $key => $category) : ?>
        <?php if (!$category->component_code) { ?>
            <?php if (!$category->is_disabled) : ?>
                <?php if ($category->security === false) : ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <div class="text-lm">
                                <?= h($category->category_name) ?>
                            </div>
                            <?php if ($category->column_na) : ?>
                                <?= $this->Form->button(
                                    'Vérifications non applicables',
                                    [
                                        'type' => 'button',
                                        'class' => 'buttonsQuestionsNa'
                                    ]
                                ); ?>
                            <?php endif; ?>
                        </div>
                        <?php foreach ($category->sub_categories as $subCategory) : ?>
                            <div class="card-body">
                                <ul class="list-group">
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="text-lm">
                                                <?= h($subCategory->name) ?>
                                            </div>
                                            <?php if ($subCategory->column_na) : ?>
                                                <?= $this->Form->button(
                                                    'Vérifications non applicables',
                                                    [
                                                        'type' => 'button',
                                                        'class' => 'buttonsQuestionsNa'
                                                    ]
                                                ); ?>
                                            <?php endif; ?>
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
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($subCategory->questions as $key => $question) : ?>
                                                        <?php if (!$question->is_disabled) : ?>
                                                            <?= $this->Form->control("responses.$question->id.question_id", [
                                                                'type' => 'hidden',
                                                                'value' => $question->id
                                                            ]); ?>
                                                            <?= $this->Form->control("responses.$question->id.response_statut", [
                                                                'type' => 'hidden',
                                                                'value' => 1
                                                            ]); ?>
                                                            <tr>
                                                                <td>
                                                                    <span
                                                                        class="<?= 'question-color question-color-' . $question->color ?>">
                                                                        <span
                                                                            class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                                    </span>
                                                                </td>
                                                                <td class="text-danger"><?= h($question->entitled) ?></td>
                                                                <td class="conformity">
                                                                    <?php if ($question->column_na) { ?>
                                                                        <?= $this->Form->radio(
                                                                            "responses.$question->id.is_conform",
                                                                            $isConformWithNa,
                                                                            [
                                                                                'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                            ]
                                                                        ); ?>
                                                                    <?php } else { ?>
                                                                        <?= $this->Form->radio(
                                                                            "responses.$question->id.is_conform",
                                                                            $isConform,
                                                                            [
                                                                                'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                            ]
                                                                        ); ?>
                                                                    <?php } ?>
                                                                </td>
                                                                <?php if ($question->is_value_required) { ?>
                                                                    <td><?= $this->Form->control("responses.$question->id.response_value", [
                                                                            'label' => false,
                                                                            'placeholder' => 'Valeur',
                                                                            'append' => $question->unity_label,
                                                                        ]); ?></td>
                                                                <?php } else { ?>
                                                                    <td style="background-color:rgba(66, 65, 65, .4)"></td>
                                                                <?php } ?>
                                                                <td class="text-danger">
                                                                    <p id="<?= 'responses-' . $question->id . '-is-conform' ?>"
                                                                       style="display:none"><?= $question->corrective_action; ?></p>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        id="<?= 'responses-' . $question->id . '-response-status' ?>"
                                                                        style="display:none">
                                                                        <?= $this->Form->radio(
                                                                            "responses.$question->id.response_statut",
                                                                            $isValid
                                                                        ); ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
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
            <?php endif; ?>
        <?php } else { ?>
            <?php $test = false; ?>
            <?php foreach ($category->component_code_categories as $component_code_category) :
                if ($component_code_category->component_code == $codeComponent || $codeComponent == '') :
                    $test = true;
                endif;
            endforeach; ?>
            <?php if ($test) : ?>
                <?php if (!$category->is_disabled) : ?>
                    <?php if ($category->security === false) : ?>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between">
                                <div class="text-lm">
                                    <?= h($category->category_name) ?>
                                </div>
                                <?php if ($category->column_na) : ?>
                                    <?= $this->Form->button(
                                        'Vérifications non applicables',
                                        [
                                            'type' => 'button',
                                            'class' => 'buttonsQuestionsNa'
                                        ]
                                    ); ?>
                                <?php endif; ?>
                            </div>
                            <?php foreach ($category->sub_categories as $subCategory) : ?>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between">
                                                <div class="text-lm">
                                                    <?= h($subCategory->name) ?>
                                                </div>
                                                <?php if ($subCategory->column_na) : ?>
                                                    <?= $this->Form->button(
                                                        'Vérifications non applicables',
                                                        [
                                                            'type' => 'button',
                                                            'class' => 'buttonsQuestionsNa'
                                                        ]
                                                    ); ?>
                                                <?php endif; ?>
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
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($subCategory->questions as $key => $question) : ?>
                                                            <?php if (!$question->is_disabled) : ?>
                                                                <?= $this->Form->control("responses.$question->id.question_id", [
                                                                    'type' => 'hidden',
                                                                    'value' => $question->id
                                                                ]); ?>
                                                                <?= $this->Form->control("responses.$question->id.response_statut", [
                                                                    'type' => 'hidden',
                                                                    'value' => 1
                                                                ]); ?>
                                                                <tr>
                                                                    <td>
                                                                    <span
                                                                        class="<?= 'question-color question-color-' . $question->color ?>">
                                                                        <span
                                                                            class="<?= 'question-color question-label-' . $question->code_key ?>"><?= $question->code_key_label_front; ?></span>
                                                                    </span>
                                                                    </td>
                                                                    <td class="text-danger"><?= h($question->entitled) ?></td>
                                                                    <td class="conformity">
                                                                        <?php if ($question->column_na) { ?>
                                                                            <?= $this->Form->radio(
                                                                                "responses.$question->id.is_conform",
                                                                                $isConformWithNa,
                                                                                [
                                                                                    'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                                ]
                                                                            ); ?>
                                                                        <?php } else { ?>
                                                                            <?= $this->Form->radio(
                                                                                "responses.$question->id.is_conform",
                                                                                $isConform,
                                                                                [
                                                                                    'onclick' => 'showCorrectiveAction(this.value, this.id)'
                                                                                ]
                                                                            ); ?>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <?php if ($question->is_value_required) { ?>
                                                                        <td><?= $this->Form->control("responses.$question->id.response_value", [
                                                                                'label' => false,
                                                                                'placeholder' => 'Valeur',
                                                                                'append' => $question->unity_label,
                                                                            ]); ?></td>
                                                                    <?php } else { ?>
                                                                        <td style="background-color:rgba(66, 65, 65, .4)"></td>
                                                                    <?php } ?>
                                                                    <td class="text-danger">
                                                                        <p id="<?= 'responses-' . $question->id . '-is-conform' ?>"
                                                                           style="display:none"><?= $question->corrective_action; ?></p>
                                                                    </td>
                                                                    <td>
                                                                        <div
                                                                            id="<?= 'responses-' . $question->id . '-response-status' ?>"
                                                                            style="display:none">
                                                                            <?= $this->Form->radio(
                                                                                "responses.$question->id.response_statut",
                                                                                $isValid
                                                                            ); ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
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
                <?php endif; ?>
            <?php endif; ?>
        <?php } ?>
    <?php endforeach; ?>
<?php endif; ?>
<script defer>
    function showCorrectiveAction(value, id) {
        id = id.substr(0, 24);
        let correctiveAction = document.getElementById(id);
        id = id.substring(0, id.length - 10) + "response-status";
        let statut = document.getElementById(id);
        if (value == 'not_ok') {
            correctiveAction.style.display = "block";
            statut.style.display = "block";
        } else {
            correctiveAction.style.display = "none";
            statut.style.display = "none";
        }
    }

    document.querySelectorAll('.conformity').forEach(td => {
        if (td.querySelectorAll('input[type="radio"]').length == 3) {
            td.querySelectorAll('input[type="radio"]')[2].classList.add('questionNA');
        }
    })

    document.querySelectorAll('.buttonsQuestionsNa').forEach(button => {
        button.addEventListener('click', (e) => {

            e.target.parentNode.nextElementSibling.querySelectorAll('.questionNA').forEach(input => {
                input.checked = true;
            })
        })
    });
</script>
