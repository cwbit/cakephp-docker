<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\OperatorForm;
use Cake\Core\Configure;

/**
 * Controls Controller
 *
 * @property \App\Model\Table\ControlsTable $Controls
 * @method \App\Model\Entity\Control[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ControlsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $idUser = $this->request->getAttribute('identity')['id'];
        $userSectors = $this->fetchTable('Users')->get($idUser, [
            'contain' => 'UserSectors',
        ]);

        $searchArray = [];
        foreach ($userSectors->user_sectors as $key => $userSector) {
            $searchArray[] = [
                'sector_id' => $userSector->sector_id,
            ];
        }
        $controls = $this->paginate($this->Controls, [
            'contain' => [
                'Checklists.Machines' => function ($query) use ($searchArray) {
                    return $query->where([
                        'OR' => $searchArray,
                    ]);
                },
                'Checklists' => [
                    'finder' => 'withDeleted',
                ],
                'Responses',
            ],
            'limit' => 10,
        ]);
        $this->set(compact('controls'));
    }

    /**
     * View method
     *
     * @param string|null $id Control id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $control = $this->Controls->get($id, [
            'contain' => [
                'ControlsCategories',
                'Checklists' => [
                    'finder' => 'withDeleted',
                    'Categories' => function ($query) {
                        return $query->order(['order_category' => 'ASC']);
                    },
                    'Categories.SubCategories.Questions' => function ($query) {
                        return $query->order(['order_question' => 'ASC']);
                    },
                    'Categories.SubCategories.Questions.Responses',
                ],
            ],
        ]);

        $this->set(compact('control'));
    }

    /**
     * Add method
     *
     * @param int $checklistId identifier
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function loginOperator()
    {
        $operator = new OperatorForm();
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['operator_ip_address'] = $this->request->clientIp();
            $codeCategory = $formData['code_categories'];
            $operatorId = $formData['userId'];
            if ($operator->execute($this->request->getData())) {
                $checklist = $this->fetchTable('Checklists')
                    ->find()
                    ->contain([
                        'Machines' => function ($query) use ($formData) {
                            return $query
                                ->contain(['Sectors'])
                                ->where(['ip_adress' => $formData['operator_ip_address']]);
                        },
                    ])
                    ->orderDesc('validated_at')
                    ->limit(1)
                    ->first();
                if (isset($checklist)) {
                    $this->Flash->success(__('Enregistrement réussi.'));

                    return $this->redirect([
                        'controller' => 'Controls',
                        'action' => 'add',
                        $checklist->id, $operatorId, $codeCategory,
                    ]);
                } else {
                    $this->Flash->error(__('Une erreur est survenue. Pas de checklist trouvée avec votre adresse IP '
                    . $formData['operator_ip_address']));
                }
            }
        }
        $this->set('operator');
    }

    /**
     * Add method
     *
     * @param int $checklistId identifier
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($checklistId, $operatorId, $codeCategory = null)
    {
        $codeComponent = '';
        if (isset($codeCategory)) {
            $codeComponent = substr($codeCategory, 0, 2);
        }

        $control = $this->Controls->newEmptyEntity();
        $checklist = $this->Controls->Checklists->get(
            $checklistId,
            [
                'contain' => [
                    'Categories.SubCategories.Questions' => function ($query) {
                        return $query->order(['order_question' => 'ASC']);
                    },
                    'Categories' => function ($query) {
                        return $query
                            ->contain(['ComponentCodeCategories'])
                            ->order(['order_category' => 'ASC']);
                    },
                ],
            ]
        );

        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['checklist_id'] = $checklistId;
            $formData['operator_id'] = $operatorId;
            $control = $this->Controls->patchEntity($control, $formData, [
                'associated' => ['Responses', 'ControlsCategories'],
            ]);
            if ($this->Controls->save($control)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => '/']);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $isConform = Configure::read('is_conform');
        $isConformWithNa = Configure::read('is_conform_with_na');
        $isValid = Configure::read('is_valid');
        $checklists = $this->Controls->Checklists->find('list')->all();
        $this->set(compact(
            'control',
            'checklist',
            'isConform',
            'codeCategory',
            'isValid',
            'isConformWithNa',
            'codeComponent'
        ));
    }

    /**
     * Delete method
     *
     * @param string|null $id Control id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $control = $this->Controls->get($id);
        if ($this->Controls->delete($control)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * isValidControl method
     *
     * @param string|null $id Control id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function toggleIsValid($id = null)
    {
        $control = $this->Controls->get($id);

        $queryUpdate = $this->Controls->query()->update()
            ->set(['is_valid' => !$control->is_valid])
            ->where(['id' => $id]);

        if ($queryUpdate->execute()) {
            $this->Flash->success(__('Enregistrement réussi.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('Une erreur est survenue.'));
    }
}
