<h1><?= __('Modifier utilisateur') ?></h1>
<?php
    echo $this->Form->control('username', ['label' => 'Identifiant']);
    echo $this->Form->control('email', ['label' => 'Email']);
    echo $this->Form->control('first_name', ['label' => 'Prénom']);
    echo $this->Form->control('last_name', ['label' => 'Nom']);
    echo $this->Form->control('role', ['options' => $roles,'label' => 'Rôle']);
?>
<div class="panel shadow-sm col-4 col-xs-6">
    <div class="panel__title_container">
        <div class="panel__title">
            <?= __('Les secteurs') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
                foreach ($userSectors as $userSectorId => $userSector) {
                    echo $this->Form->control(
                        'user_sectors..sector_id',
                        [
                            'type' => 'checkbox',
                            'label' => $userSector,
                            'value' => $userSectorId,
                            'checked' => in_array($userSectorId, $associatedSectorIds),
                        ]
                    );
                }
            ?>
        </div>
    </div>
</div>
