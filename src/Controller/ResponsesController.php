<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

/**
 * Responses Controller
 *
 * @property \App\Model\Table\ResponsesTable $Responses
 * @method \App\Model\Entity\Response[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ResponsesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Controls', 'Questions'],
        ];
        $responses = $this->paginate($this->Responses);

        $this->set(compact('responses'));
    }

    /**
     * View method
     *
     * @param string|null $id Response id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $response = $this->Responses->get($id, [
            'contain' => ['Questions.SubCategories.Categories.Checklists'],
        ]);

        $this->set(compact('response'));
    }

    /**
     * Add method
     *
     * @param int $controlId identifier
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($controlId)
    {
        $response = $this->Responses->newEmptyEntity();
        $control = $this->Responses->Controls->get($controlId, [
            'contain' => ['Checklists.Categories.SubCategories.Questions', 'ControlsCategories'],
        ]);
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['control_id'] = $controlId;
            $responses = $this->Responses->newEntities($formData);
            if ($this->Responses->saveMany($responses)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Checklists', 'action' => 'index']);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $controls = $this->Responses->Controls->find('list')->all();
        $questions = $this->Responses->Questions->find('list')->all();
        $this->set(compact('response', 'control', 'controls', 'questions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Response id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $response = $this->Responses->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $response = $this->Responses->patchEntity($response, $this->request->getData());
            if ($this->Responses->save($response)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Controls', 'action' => 'view', $response->control_id]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $isConform = Configure::read('is_conform');
        $controls = $this->Responses->Controls->find('list')->all();
        $questions = $this->Responses->Questions->find('list')->all();
        $this->set(compact('response', 'controls', 'questions', 'isConform'));
    }
}
