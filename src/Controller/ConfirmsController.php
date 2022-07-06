<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Confirms Controller
 *
 * @property \App\Model\Table\ConfirmsTable $Confirms
 * @method \App\Model\Entity\Confirm[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ConfirmsController extends AppController
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
        $confirms = $this->paginate($this->Confirms);

        $this->set(compact('confirms'));
    }

    /**
     * View method
     *
     * @param string|null $id Confirm id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $confirm = $this->Confirms->get($id, [
            'contain' => ['Controls', 'Questions'],
        ]);

        $this->set(compact('confirm'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $confirm = $this->Confirms->newEmptyEntity();
        if ($this->request->is('post')) {
            $confirm = $this->Confirms->patchEntity($confirm, $this->request->getData());
            if ($this->Confirms->save($confirm)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $controls = $this->Confirms->Controls->find('list')->all();
        $questions = $this->Confirms->Questions->find('list')->all();
        $this->set(compact('confirm', 'controls', 'questions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Confirm id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $confirm = $this->Confirms->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $confirm = $this->Confirms->patchEntity($confirm, $this->request->getData());
            if ($this->Confirms->save($confirm)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $controls = $this->Confirms->Controls->find('list')->all();
        $questions = $this->Confirms->Questions->find('list')->all();
        $this->set(compact('confirm', 'controls', 'questions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Confirm id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $confirm = $this->Confirms->get($id);
        if ($this->Confirms->delete($confirm)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
