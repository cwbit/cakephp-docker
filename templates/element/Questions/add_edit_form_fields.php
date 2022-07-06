<div class="row">
    <div class="col-12">
        <?= $this->Form->control('is_disabled', [
            'label' => 'Désactiver la question',
        ]); ?>
    </div>
    <div class="col-12">
        <?= $this->Form->control('entitled', [
            'label' => 'Intitulé',
        ]); ?>
    </div>

    <div class="col-6">
        <div class="mb-3 form-group">
            <?= $this->Form->label(
                'color',
                __('Couleur'),
                [
                    'class' => 'form-label'
                ]
            ); ?>
            <?= $this->Form->select(
                'color',
                $colors,
                [
                    'label' => ['text' => __('Couleur')]
                ]
            ); ?>
        </div>
    </div>

    <div class="col-6">
        <div class="mb-3 form-group">
            <?= $this->Form->label(
                'code_key',
                __('Code'),
                [
                    'class' => 'form-label'
                ]
            ); ?>
            <?= $this->Form->select(
                'code_key',
                $keyCodes,
                [
                    'label' => ['text' => __('Code')],
                    'escape' => false,
                ]
            ); ?>
        </div>
    </div>

    <div class="col-12">
        <?= $this->Form->control('leader_alert', [
            'label' => 'Alerte CE',
        ]); ?>
        <?= $this->Form->control('column_na', [
            'label' => 'Non Applicable',
        ]); ?>
    </div>

    <div class="col-12">
        <?= $this->Form->control('corrective_action', [
            'label' => 'Action corrective',
            'required' => false,
            'type' => 'textarea',
        ]); ?>
    </div>

    <div class="col-12">
        <?= $this->Form->control('is_value_required', [
            'label' => 'Saisie de la valeur obligatoire',
            'onclick' => 'valueRequired()'
        ]); ?>
    </div>

    <div class="col-4">
        <div class="mb-3 form-group">
            <?= $this->Form->label(
                'unity',
                __('Unité'),
                [
                    'class' => 'form-label'
                ]
            ); ?>
            <?= $this->Form->select(
                'unity',
                $units,
                ['empty' => 'Aucune'],
                [
                    'label' => ['text' => __('Unité')]
                ]
            ); ?>
        </div>
    </div>
</div>
