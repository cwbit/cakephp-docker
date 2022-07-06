<?= $this->Form->control("response_value"); ?>
<div class="row">
    <div class="col-2">
        <?= 'Conformité' ?>
        <?= $this->Form->radio("is_conform", $isConform); ?>
    </div>
    <div class="col-2">
        <?= 'Statut' ?>
        <?= $this->Form->radio("response_statut", $isConform); ?>
    </div>
</div>
