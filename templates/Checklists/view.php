<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checklist $checklist
 */
?>
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
<?php if (!empty($checklist->categories)) : ?>

    <?php foreach ($checklist->categories as $categories) : ?>
        <?php if ($categories->security === true) : ?>
            <div class="card mb-4" style="background-color:rgba(247, 175, 79, .2)">
                <div class="card-header d-flex justify-content-between">
                    <div class="text-lm">
                        <?= h($categories->category_name) ?>
                        <?= '(' . $categories->is_active_category . ')' ?>
                    </div>
                </div>
                <div class="card-body">

                    <ul class="list-group">
                        <?php
                        foreach ($categories->sub_categories as $subCategory) : ?>
                            <div class="card mb-4" style="background-color:rgba(247, 175, 79, .1)">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="text-lm">
                                        <?= h($subCategory->name) ?>
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
                                        </li>
                                    <?php endforeach; ?>
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
                    </div>
                </div>
                <div class="card-body">

                    <ul class="list-group">
                        <?php
                        foreach ($categories->sub_categories as $subCategory) : ?>
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="text-lm">
                                        <?= h($subCategory->name) ?>
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
                                        </li>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                </div>
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
</div>
