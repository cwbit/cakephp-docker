<?php
declare(strict_types=1);

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class OperatorForm extends Form
{
    protected function buildSchema(Schema $schema)
    {
        return $schema
            ->addField('operator_id', ['type' => 'string'])
            ->addField('code_categories', ['type' => 'string'])
            ->addField('operator_ip_address', 'string');
    }

    protected function _buildValidator(Validator $validator)
    {
        return $validator->add('operator_id', 'length', [
            'rule' => ['maxLength', 13],
            'message' => "L'idenditiant de l'opérateur est requis.",
        ])->add('code_categories', 'length', [
            'rule' => ['minLength', 2],
            'message' => 'Le code en cours est requis',
        ]);
    }
}
