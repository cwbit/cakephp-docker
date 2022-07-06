<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

/**
 * Questions Controller
 *
 * @property \App\Model\Table\QuestionsTable $Questions
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QuestionsController extends AppController
{
    /**
     * @param int $subCategoryId identifier
     * @return \Cake\Http\Response|void|null
     */
    public function add($subCategoryId)
    {
        $number = $this->Questions->query()->where(['subCategory_id' => $subCategoryId])->count();
        $number++;
        $question = $this->Questions->newEmptyEntity();
        $subCategory = $this->Questions->SubCategories->get(
            $subCategoryId,
            [
                'contain' => ['Categories'],
            ]
        );
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['subCategory_id'] = $subCategoryId;
            $question = $this->Questions->patchEntity($question, $formData);
            if ($this->Questions->save($question)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect([
                    'controller' => 'Checklists',
                    'action' => 'edit',
                    $subCategory->category->checklist_id,
                ]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        $colors = Configure::read('color_labels');
        $keyCodes = Configure::read('key_code_labels_admin');
        $units = Configure::read('unit_labels');

        $this->set(compact('question', 'colors', 'keyCodes', 'units', 'subCategory', 'number'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Question id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $question = $this->Questions->get($id, [
            'contain' => ['SubCategories.Categories'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $question = $this->Questions->patchEntity($question, $this->request->getData());
            if ($this->Questions->save($question)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(
                    ['controller' => 'Checklists', 'action' => 'edit', $question->sub_category->category->checklist_id]
                );
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        $colors = Configure::read('color_labels');
        $keyCodes = Configure::read('key_code_labels_admin');
        $units = Configure::read('unit_labels');
        $this->set(compact('question', 'colors', 'keyCodes', 'units'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Question id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $question = $this->Questions->get($id, [
            'contain' => ['SubCategories.Categories'],
        ]);
        if ($this->Questions->delete($question)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        return $this->redirect([
            'controller' => 'Checklists',
            'action' => 'edit',
            $question->sub_category->category->checklist_id,
        ]);
    }

    /**
     * changePositionQuestion method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function changePositionQuestion()
    {
        $this->request->allowMethod(['post']);

        $postData = $this->request->getData();
        $question = $this->Questions->get($postData['id']);
        $newOrderValue = $postData['direction'] == 'up' ? $question->order_question - 1 : $question->order_question + 1;
        $otherquestion = $this->Questions->find()
            ->where([
                'subCategory_id' => $question->subCategory_id,
                'order_question' => $newOrderValue,
            ])
            ->first();

        $queryUpdate1 = $this->Questions->query()->update()
            ->set(['order_question' => $newOrderValue])
            ->where(['id' => $question->id]);

        $queryUpdate2 = $this->Questions->query()->update()
            ->set(['order_question' => $question->order_question])
            ->where(['id' => $otherquestion->id]);

        if ($queryUpdate1->execute() && $queryUpdate2->execute()) {
            $this->Flash->success(__('Enregistrement réussi.'));

            return $this->redirect(['controller' => 'Checklists', 'action' => 'edit', $postData['checklist_id']]);
        }
        $this->Flash->error(__('Une erreur est survenue.'));
    }
}
