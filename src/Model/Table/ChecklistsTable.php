<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Checklists Model
 *
 * @property \App\Model\Table\MachinesTable&\Cake\ORM\Association\BelongsTo $Machines
 * @method \App\Model\Entity\Checklist newEmptyEntity()
 * @method \App\Model\Entity\Checklist newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Checklist[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Checklist get($primaryKey, $options = [])
 * @method \App\Model\Entity\Checklist findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Checklist patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Checklist[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Checklist|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Checklist saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Checklist[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Checklist[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Checklist[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Checklist[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ChecklistsTable extends Table
{
    use SoftDeleteTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('checklists');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Machines', [
            'foreignKey' => 'machine_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('CustomUsers', [
            'foreignKey' => 'author_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ParentChecklists', [
            'className' => 'Checklists',
            'foreignKey' => 'parent_checklist_id',
        ]);

        $this->hasMany('ChildrenChecklists')
            ->setClassName('Checklists')
            ->setConditions(['parent_checklist_id IS NOT NULL'])
            ->setForeignKey('parent_checklist_id')
            ->setBindingKey('parent_checklist_id');

        $this->hasMany('Categories', [
            'foreignKey' => 'checklist_id',
        ])->setDependent(true);

        // add Duplicatable behavior
        $this->addBehavior('Duplicatable.Duplicatable', [
            // table finder
            'finder' => 'all',
            // duplicate also items and their properties
            'contain' => [
                'Categories.SubCategories.Questions',
            ],
            // remove created field from both invoice and items
            'remove' => [
                'created', 'modified',
                'Categories.created',
                'Categories.modified',
                'SubCategories.created',
                'SubCategories.modified',
                'Questions.created',
                'Questions.modified',
            ],
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('checklist_name')
            ->maxLength('checklist_name', 255)
            ->requirePresence('checklist_name', 'create')
            ->notEmptyString('checklist_name');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('machine_id', 'Machines'), ['errorField' => 'machine_id']);

        return $rules;
    }

    /**
     * @return array
     */
    public function findWithDeleted()
    {
        return $this->find('all', ['withDeleted']);
    }
}
