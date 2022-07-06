<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SubCategories Controller
 *
 * @property \App\Model\Table\SubCategoriesTable $SubCategories
 * @method \App\Model\Entity\SubCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SubCategoriesController extends AppController
{
    /**
     * @param int $categoryId identifier
     * @return \Cake\Http\Response|void|null
     */
    public function add($categoryId)
    {
        $subCategory = $this->SubCategories->newEmptyEntity();
        $category = $this->SubCategories->Categories->get($categoryId);
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['category_id'] = $categoryId;
            $subCategory = $this->SubCategories->patchEntity($subCategory, $formData);
            if ($this->SubCategories->save($subCategory)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $category->checklist_id]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $categories = $this->SubCategories->Categories->find('list', ['limit' => 200])->all();
        $this->set(compact('subCategory', 'categories', 'category'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Sub Category id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $subCategory = $this->SubCategories->get($id, [
            'contain' => ['Categories'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subCategory = $this->SubCategories->patchEntity($subCategory, $this->request->getData());
            if ($this->SubCategories->save($subCategory)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(
                    ['controller' => 'Checklists', 'action' => 'edit', $subCategory->category->checklist_id]
                );
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $categories = $this->SubCategories->Categories->find('list', ['limit' => 200])->all();
        $this->set(compact('subCategory', 'categories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Sub Category id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $subCategory = $this->SubCategories->get($id, [
            'contain' => ['Categories'],
        ]);
        if ($this->SubCategories->delete($subCategory)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $checklist = $subCategory->category->checklist_id;

        return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $checklist]);
    }
}
