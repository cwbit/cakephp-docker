<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Questions Model
 *
 * @property \App\Model\Table\SubCategoriesTable&\Cake\ORM\Association\BelongsTo $SubCategories
 * @method \App\Model\Entity\Question newEmptyEntity()
 * @method \App\Model\Entity\Question newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Question[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Question get($primaryKey, $options = [])
 * @method \App\Model\Entity\Question findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Question patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Question[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Question|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Question saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QuestionsTable extends Table
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

        $this->setTable('questions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SubCategories', [
            'foreignKey' => 'subCategory_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Confirmations', [
            'foreignKey' => 'question_id',
        ]);
        $this->hasMany('Confirms', [
            'foreignKey' => 'question_id',
        ]);
        $this->hasMany('Responses', [
            'foreignKey' => 'question_id',
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
            ->scalar('entitled')
            ->maxLength('entitled', 255)
            ->requirePresence('entitled', 'create')
            ->notEmptyString('entitled');

        $validator
            ->scalar('color')
            ->maxLength('color', 255)
            ->requirePresence('color', 'create')
            ->notEmptyString('color');

        $validator
            ->scalar('code_key')
            ->maxLength('code_key', 255)
            ->requirePresence('code_key', 'create')
            ->notEmptyString('code_key');

        $validator
            ->boolean('column_na')
            ->requirePresence('column_na', 'create');

        $validator
            ->scalar('corrective_action')
            ->maxLength('corrective_action', 255)
            ->allowEmptyString('corrective_action');

        $validator
            ->boolean('leader_alert')
            ->requirePresence('leader_alert', 'create')
            ->notEmptyString('leader_alert');

        $validator
            ->boolean('is_value_required')
            ->requirePresence('is_value_required', 'create')
            ->notEmptyString('is_value_required');

        $validator
            ->boolean('is_disabled')
            ->requirePresence('is_disabled', 'create')
            ->notEmptyString('is_disabled');

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
        $rules->add($rules->existsIn('subCategory_id', 'SubCategories'), ['errorField' => 'subCategory_id']);

        return $rules;
    }
}
