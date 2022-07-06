<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ComponentCodeCategories Controller
 *
 * @property \App\Model\Table\ComponentCodeCategoriesTable $ComponentCodeCategories
 * @method \App\Model\Entity\ComponentCodeCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ComponentCodeCategoriesController extends AppController
{
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($categoryId)
    {
        $category = $this->ComponentCodeCategories->Categories->get($categoryId);
        $componentCodeCategory = $this->ComponentCodeCategories->newEmptyEntity();
        if ($this->request->is('post')) {
            $componentCodeCategory = $this->ComponentCodeCategories->patchEntity(
                $componentCodeCategory,
                $this->request->getData()
            );
            if ($this->ComponentCodeCategories->save($componentCodeCategory)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $category->checklist_id]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $this->set(compact('componentCodeCategory', 'category'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Component Code Category id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $componentCodeCategory = $this->ComponentCodeCategories->get($id, [
            'contain' => ['Categories'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $componentCodeCategory = $this->ComponentCodeCategories->patchEntity(
                $componentCodeCategory,
                $this->request->getData()
            );
            if ($this->ComponentCodeCategories->save($componentCodeCategory)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect([
                    'controller' => 'Categories', 'action' => 'edit', $componentCodeCategory->category->id,
                ]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $this->set(compact('componentCodeCategory'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Component Code Category id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $componentCodeCategory = $this->ComponentCodeCategories->get($id);
        if ($this->ComponentCodeCategories->delete($componentCodeCategory)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        return $this->redirect(['controller' => 'Categories', 'action' => 'edit', $componentCodeCategory->category_id]);
    }
}
