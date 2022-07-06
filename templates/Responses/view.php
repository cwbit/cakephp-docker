<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Response $response
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Response'), ['action' => 'edit', $response->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Response'), ['action' => 'delete', $response->id], ['confirm' => __('Are you sure you want to delete # {0}?', $response->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Responses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Response'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="responses view content">
            <h3><?= h($response->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Control') ?></th>
                    <td><?= $response->has('control') ? $this->Html->link($response->control->id, ['controller' => 'Controls', 'action' => 'view', $response->control->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Question') ?></th>
                    <td><?= $response->has('question') ? $this->Html->link($response->question->id, ['controller' => 'Questions', 'action' => 'view', $response->question->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Response Value') ?></th>
                    <td><?= h($response->response_value) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($response->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Response Statut') ?></th>
                    <td><?= $this->Number->format($response->response_statut) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($response->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($response->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Conform') ?></th>
                    <td><?= $response->is_conform ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
