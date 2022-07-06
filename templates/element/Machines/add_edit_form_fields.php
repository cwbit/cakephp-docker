<?= $this->Form->control('machine_name', [
    'label' => 'Nom de la machine',
]); ?>

<?= $this->Form->control('sector_id', [
    'options' => $sectors,
    'label' => 'Secteur',
]); ?>

<?= $this->Form->control('ip_adress', [
    'label' => 'Adresse IP',
]); ?>

<?= $this->Form->control('is_disabled', [
    'type' => 'hidden',
    'default' => 0
]); ?>
