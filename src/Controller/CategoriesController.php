<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Categories Controller
 *
 * @property \App\Model\Table\CategoriesTable $Categories
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CategoriesController extends AppController
{
    /**
     * @param int $checklistId identifier
     * @return \Cake\Http\Response|void|null
     */
    public function add($checklistId)
    {
        $number = $this->Categories->query()->where(['checklist_id' => $checklistId])->count();
        $number++;
        $category = $this->Categories->newEmptyEntity();
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['checklist_id'] = $checklistId;
            $category = $this->Categories->patchEntity($category, $formData, [
                'associated' => ['ComponentCodeCategories',],
            ]);
            if ($this->Categories->save($category)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $checklistId]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $this->set(compact('category', 'checklistId', 'number'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $category = $this->Categories->get($id, [
            'contain' => [
                'ComponentCodeCategories',
            ],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $category = $this->Categories->patchEntity($category, $this->request->getData());
            if ($this->Categories->save($category)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $category->checklist_id]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $this->set(compact('category'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $category = $this->Categories->get($id);
        if ($this->Categories->delete($category)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $category->checklist_id]);
    }

    /**
     * changePositionCategory method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function changePositionCategory()
    {
        $this->request->allowMethod(['post']);

        $postData = $this->request->getData();
        $category = $this->Categories->get($postData['id']);
        $newOrderValue = $postData['direction'] == 'up' ? $category->order_category - 1 : $category->order_category + 1;
        $otherCategory = $this->Categories->find()
            ->where([
                'checklist_id' => $category->checklist_id,
                'order_category' => $newOrderValue,
            ])
            ->first();

        $queryUpdate1 = $this->Categories->query()->update()
            ->set(['order_category' => $newOrderValue])
            ->where(['id' => $category->id]);

        $queryUpdate2 = $this->Categories->query()->update()
            ->set(['order_category' => $category->order_category])
            ->where(['id' => $otherCategory->id]);

        if ($queryUpdate1->execute() && $queryUpdate2->execute()) {
            $this->Flash->success(__('Enregistrement réussi.'));

            return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $category->checklist_id]);
        }
        $this->Flash->error(__('Une erreur est survenue.'));
    }
}
