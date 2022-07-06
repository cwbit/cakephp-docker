<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Machines Controller
 *
 * @property \App\Model\Table\MachinesTable $Machines
 * @method \App\Model\Entity\Machine[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MachinesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Sectors'],
            'order' => ['Machines.machine_name' => 'asc'],
            'limit' => 10,
        ];
        $machines = $this->paginate($this->Machines);

        $this->set(compact('machines'));
    }

    /**
     * View method
     *
     * @param string|null $id Machine id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $machine = $this->Machines->get($id, [
            'contain' => ['Sectors'],
        ]);

        $this->set(compact('machine'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $machine = $this->Machines->newEmptyEntity();
        if ($this->request->is('post')) {
            $machine = $this->Machines->patchEntity($machine, $this->request->getData());
            if ($this->Machines->save($machine)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $sectors = $this->Machines->Sectors->find('list')->all();
        $this->set(compact('machine', 'sectors'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Machine id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $machine = $this->Machines->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $machine = $this->Machines->patchEntity($machine, $this->request->getData());
            if ($this->Machines->save($machine)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $sectors = $this->Machines->Sectors->find('list')->all();
        $this->set(compact('machine', 'sectors'));
    }

    /**
     * Disable/Enable method
     *
     * @param string|null $id Machine id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function toggleIsDisabled($id = null)
    {
        $this->request->allowMethod(['post']);
        $machine = $this->Machines->get($id);

        $queryUpdate = $this->Machines->query()->update()
            ->set(['is_disabled' => !$machine->is_disabled])
            ->where(['id' => $id]);

        if ($queryUpdate->execute()) {
            $this->Flash->success(__('Enregistrement réussi.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('Une erreur est survenue.'));
    }
}
