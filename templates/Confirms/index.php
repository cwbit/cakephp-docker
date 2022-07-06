<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Confirm[]|\Cake\Collection\CollectionInterface $confirms
 */
?>
<div class="confirms index content">
    <?= $this->AuthLink->link(__('Nouvelle réponse'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Confirms') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('contrôle') ?></th>
                    <th><?= $this->Paginator->sort('question') ?></th>
                    <th><?= $this->Paginator->sort('création') ?></th>
                    <th><?= $this->Paginator->sort('modification') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($confirms as $confirm): ?>
                <tr>
                    <td><?= $this->Number->format($confirm->id) ?></td>
                    <td><?= $confirm->has('control') ? $this->AuthLink->link($confirm->control->id, ['controller' => 'Controls', 'action' => 'view', $confirm->control->id]) : '' ?></td>
                    <td><?= $confirm->has('question') ? $this->AuthLink->link($confirm->question->id, ['controller' => 'Questions', 'action' => 'view', $confirm->question->id]) : '' ?></td>
                    <td><?= h($confirm->created) ?></td>
                    <td><?= h($confirm->modified) ?></td>
                    <td class="actions">
                        <?= $this->AuthLink->link(__('Voir'), ['action' => 'view', $confirm->id]) ?>
                        <?= $this->AuthLink->link(__('Editer'), ['action' => 'edit', $confirm->id]) ?>
                        <?= $this->AuthLink->postLink(__('Supprimer'), ['action' => 'delete', $confirm->id], ['confirm' => __('Are you sure you want to delete # {0}?', $confirm->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('premier')) ?>
            <?= $this->Paginator->prev('< ' . __('avant')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('après') . ' >') ?>
            <?= $this->Paginator->last(__('dernier') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, affichant {{current}} enreigstrement(s) sur {{count}} au total')) ?></p>
    </div>
</div>
