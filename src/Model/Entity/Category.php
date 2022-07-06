<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Category Entity
 *
 * @property int $id
 * @property string $category_name
 * @property int $checklist_id
 * @property bool $component_code
 * @property bool $column_na
 * @property bool $security
 * @property bool $is_disabled
 * @property int $order_category
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Checklist $checklist
 * @property \App\Model\Entity\SubCategory[] $subCategory
 * @property \App\Model\Entity\ComponentCodeCategory[] $componentCodeCategory
 */
class Category extends Entity
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
    protected function _getIsActiveCategory()
    {
        return Configure::read("is_active_category.{$this->is_disabled}");
    }
}
