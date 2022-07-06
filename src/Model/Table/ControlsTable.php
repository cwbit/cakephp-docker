<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Controls Model
 *
 * @property \App\Model\Table\ChecklistsTable&\Cake\ORM\Association\BelongsTo $Checklists
 * @property \App\Model\Table\ResponsesTable&\Cake\ORM\Association\HasMany $Responses
 * @property \App\Model\Table\ControlsCategoriesTable&\Cake\ORM\Association\HasMany $ControlsCategories
 * @method \App\Model\Entity\Control newEmptyEntity()
 * @method \App\Model\Entity\Control newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Control[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Control get($primaryKey, $options = [])
 * @method \App\Model\Entity\Control findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Control patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Control[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Control|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Control saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Control[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Control[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Control[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Control[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ControlsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('controls');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Checklists', [
            'foreignKey' => 'checklist_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Responses', [
            'foreignKey' => 'control_id',
        ])->setDependent(true);
        $this->hasMany('ControlsCategories', [
            'foreignKey' => 'control_id',
        ])->setDependent(true);
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
        $rules->add($rules->existsIn('checklist_id', 'Checklists'), ['errorField' => 'checklist_id']);

        return $rules;
    }
}
