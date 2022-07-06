<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Question Entity
 *
 * @property int $id
 * @property int $sub_category_id
 * @property string $entitled
 * @property string $color
 * @property bool $column_na
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $code_key
 * @property string $unity
 * @property string $corrective_action
 * @property bool $leader_alert
 * @property bool $is_value_required
 * @property bool $is_disabled
 * @property int $order_question
 *
 * @property \App\Model\Entity\SubCategory $subCategory
 * @property \App\Model\Entity\Confirmation[] $confirmations
 * @property \App\Model\Entity\Confirm[] $confirms
 * @property \App\Model\Entity\Response[] $responses
 */
class Question extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * @return string
     */
    protected function _getCodeKeyLabelAdmin()
    {
        return Configure::read("key_code_labels_admin.{$this->code_key}");
    }

    /**
     * @return string
     */
    protected function _getCodeKeyLabelFront()
    {
        return Configure::read("key_code_labels_front.{$this->code_key}");
    }

    /**
     * @return string
     */
    protected function _getUnityLabel()
    {
        return Configure::read("unit_labels.{$this->unity}");
    }

    /**
     * @return string
     */
    protected function _getIsActiveQuestion()
    {
        return Configure::read("is_active_question.{$this->is_disabled}");
    }
}
