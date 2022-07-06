<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Response Entity
 *
 * @property int $id
 * @property int $control_id
 * @property int $question_id
 * @property string $is_conform
 * @property string $response_value
 * @property int $response_statut
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Control $control
 * @property \App\Model\Entity\Question $question
 */
class Response extends Entity
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
    protected function _getIsConformValue()
    {
        return Configure::read("is_conform_with_na.{$this->is_conform}");
    }

    /**
     * @return string
     */
    protected function _getResponseStatutValue()
    {
        return Configure::read("is_valid.{$this->response_statut}");
    }
}
