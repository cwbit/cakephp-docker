<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ControlsCategories Controller
 *
 * @property \App\Model\Table\ControlsCategoriesTable $ControlsCategories
 * @method \App\Model\Entity\ControlsCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ControlsCategoriesController extends AppController
{
    /**
     * Add method
     *
     * @param int $controlId identifier
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($controlId)
    {
        $controlsCategory = $this->ControlsCategories->newEmptyEntity();
        $control = $this->ControlsCategories->Controls->get($controlId);
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['control_id'] = $controlId;
            $controlsCategory = $this->ControlsCategories->patchEntity($controlsCategory, $formData);
            if ($this->ControlsCategories->save($controlsCategory)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Responses', 'action' => 'add', $controlId]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $controls = $this->ControlsCategories->Controls->find('list')->all();
        $this->set(compact('controlsCategory', 'control', 'controls'));
    }
}
