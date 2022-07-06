<?php if ($countQuestions == 1) { ?>
<?php } elseif ($question->order_question == 1) { ?>
    <?= $this->AuthLink->postLink(
        '<i class="bi bi-arrow-down-short"></i>',
        [
            'controller' => 'Questions', 'action' => 'changePositionQuestion'
        ],
        [
            'class' => 'btn btn-sm btn-outline-primary',
            'escape' => false,
            'data' => [
                'checklist_id' => $checklist->id,
                'id' => $question->id,
                'direction' => 'down',
            ],
        ]
    ) ?>
 <?php } elseif ($question->order_question == $countQuestions) { ?>
 <?= $this->AuthLink->postLink(
        '<i class="bi bi-arrow-up-short"></i>',
        [
            'controller' => 'Questions', 'action' => 'changePositionQuestion'
        ],
        [
            'class' => 'btn btn-sm btn-outline-primary',
            'escape' => false,
            'data' => [
                'checklist_id' => $checklist->id,
                'id' => $question->id,
                'direction' => 'up',
            ],
        ]
    ) ?>
 <?php } else { ?>
 <?= $this->AuthLink->postLink(
        '<i class="bi bi-arrow-down-short"></i>',
        [
            'controller' => 'Questions', 'action' => 'changePositionQuestion'
        ],
        [
            'class' => 'btn btn-sm btn-outline-primary',
            'escape' => false,
            'data' => [
                'checklist_id' => $checklist->id,
                'id' => $question->id,
                'direction' => 'down',
            ],
        ]
    ) ?>
 <?= $this->AuthLink->postLink(
        '<i class="bi bi-arrow-up-short"></i>',
        [
            'controller' => 'Questions', 'action' => 'changePositionQuestion'
        ],
        [
            'class' => 'btn btn-sm btn-outline-primary',
            'escape' => false,
            'data' => [
                'checklist_id' => $checklist->id,
                'id' => $question->id,
                'direction' => 'up',
            ],
        ]
    ) ?>
<?php } ?>
