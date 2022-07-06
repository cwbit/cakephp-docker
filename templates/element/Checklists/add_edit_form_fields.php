<?= $this->Form->control('checklist_name', [
    'label' => 'Nom de la checklist',
]); ?>

<?= $this->Form->control('machine_id', [
    'options' => $machines,
    'label' => 'Machine',
]); ?>

<?= $this->Form->control('status', [
    'type' => 'hidden',
    'value' => 'Approuvée'
]); ?>

<?= $this->Form->control('version', [
    'type' => 'hidden',
    'value' => 1
]); ?>
