<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Confirm $confirm
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->AuthLink->link(__('Editer réponse'), ['action' => 'edit', $confirm->id], ['class' => 'side-nav-item']) ?>
            <?= $this->AuthLink->postLink(__('Supprimer la réponse'), ['action' => 'delete', $confirm->id], ['confirm' => __('Etes-vous sûr de vouloir supprimer?', $confirm->id), 'class' => 'side-nav-item']) ?>
            <?= $this->AuthLink->link(__('Liste des réponses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->AuthLink->link(__('Nouvelle réponse'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="confirms view content">
            <h3><?= h($confirm->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Contrôle') ?></th>
                    <td><?= $confirm->has('control') ? $this->AuthLink->link($confirm->control->id, ['controller' => 'Controls', 'action' => 'view', $confirm->control->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Question') ?></th>
                    <td><?= $confirm->has('question') ? $this->AuthLink->link($confirm->question->id, ['controller' => 'Questions', 'action' => 'view', $confirm->question->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($confirm->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Création') ?></th>
                    <td><?= h($confirm->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modification') ?></th>
                    <td><?= h($confirm->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
