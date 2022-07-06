<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Response[]|\Cake\Collection\CollectionInterface $responses
 */
?>
<div class="responses index content">
    <?= $this->Html->link(__('New Response'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Responses') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('control_id') ?></th>
                    <th><?= $this->Paginator->sort('question_id') ?></th>
                    <th><?= $this->Paginator->sort('is_conform') ?></th>
                    <th><?= $this->Paginator->sort('response_value') ?></th>
                    <th><?= $this->Paginator->sort('response_statut') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $response): ?>
                <tr>
                    <td><?= $this->Number->format($response->id) ?></td>
                    <td><?= $response->has('control') ? $this->Html->link($response->control->id, ['controller' => 'Controls', 'action' => 'view', $response->control->id]) : '' ?></td>
                    <td><?= $response->has('question') ? $this->Html->link($response->question->id, ['controller' => 'Questions', 'action' => 'view', $response->question->id]) : '' ?></td>
                    <td><?= h($response->is_conform) ?></td>
                    <td><?= h($response->response_value) ?></td>
                    <td><?= $this->Number->format($response->response_statut) ?></td>
                    <td><?= h($response->created) ?></td>
                    <td><?= h($response->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $response->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $response->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $response->id], ['confirm' => __('Are you sure you want to delete # {0}?', $response->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
