<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;

/**
 * Checklists Controller
 *
 * @property \App\Model\Table\ChecklistsTable $Checklists
 * @method \App\Model\Entity\Checklist[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ChecklistsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $containParams = [
            'Machines',
        ];

        $this->paginate = [
            'contain' => $containParams,
            'order' => ['Checklists.checklist_name' => 'asc'],
            'limit' => 10,
        ];

        $checklists = $this->Checklists->find()
            ->where(['parent_checklist_id IS NULL', 'validated_at IS NOT NULL']);

        $checklists = $this->paginate($checklists);

        $checklists = collection($checklists)
            ->map(function ($checklist) use ($containParams) {

                $children = $this->Checklists->find()
                    ->where(['parent_checklist_id' => $checklist->id, 'validated_at IS NOT NULL'])
                    ->contain($containParams)
                    ->orderDesc('validated_at')
                    ->limit(1)
                    ->first();

                return $children ?: $checklist;
            });
            $idUser = $this->request->getAttribute('identity')['id'];

        $this->set(compact('checklists', 'idUser'));
    }

    /**
     * modifiedChecklists method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function modifiedChecklists()
    {
        $checklists = $this->Checklists
            ->find('withDeleted')
            ->contain([
                'Machines' => function ($query) {
                    return $query->where(['parent_checklist_id IS NOT' => null]);
                },
                'CustomUsers',
            ]);
        $checklists = $this->paginate($checklists, [
            'limit' => 10,
            'order' => ['Checklists.created' => 'asc'],
        ]);
        $this->set(compact('checklists'));
    }

    /**
     * View method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $checklist = $this->Checklists->get($id, [
            'contain' => [
                'Machines',
                'Categories.ComponentCodeCategories',
                'Categories' => function ($query) {
                    return $query->order(['order_category' => 'ASC']);
                },
                'Categories.SubCategories.Questions' => function ($query) {
                    return $query->order(['order_question' => 'ASC']);
                },
            ],
        ]);
        $countCategories = $this->Checklists->Categories->query()->where(['checklist_id' => $id])->count();

        $this->set(compact('checklist', 'countCategories'));
    }

    /**
     * ViewModifiedChecklist method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewModifiedChecklist($id = null)
    {
        $checklist = $this->Checklists->find('withDeleted')
            ->contain([
                'Machines',
                'Categories.SubCategories.Questions',
                'ChildrenChecklists' => function ($query) use ($id) {
                    return $query->find('withDeleted')
                        ->contain([
                            'Machines',
                            'Categories.SubCategories.Questions',
                        ])
                        ->where(['ChildrenChecklists.id <>' => $id]);
                },
                'ParentChecklists' => function ($query) use ($id) {
                    return $query->find('withDeleted')
                        ->contain([
                            'Machines',
                            'Categories.SubCategories.Questions',
                        ])
                        ->where(['ParentChecklists.id <>' => $id]);
                },
            ])
            ->where(['Checklists.id' => $id])
            ->first()
            ->toArray();

        $checklists = collection($checklist['children_checklists'] ?? [])
            ->append([$checklist['parent_checklist']])
            ->sortBy('version', SORT_ASC)
            ->toArray();

        $isActiveCategory = Configure::read('is_active_category');
        $colors = Configure::read('color_labels');
        $keyCodes = Configure::read('key_code_labels_front');
        $isActiveQuestion = Configure::read('is_active_question');

        $this->set(compact(
            'checklist',
            'id',
            'checklists',
            'isActiveCategory',
            'colors',
            'keyCodes',
            'isActiveQuestion'
        ));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $checklist = $this->Checklists->newEmptyEntity();
        if ($this->request->is('post')) {
            $formData = $this->request->getData();
            $formData['validated_at'] = FrozenTime::now();
            $checklist = $this->Checklists->patchEntity($checklist, $formData);
            if ($this->Checklists->save($checklist)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['action' => 'edit', $checklist->id]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $machines = $this->Checklists->Machines->find('list')
            ->contain(['Sectors']);
        // TODO : SORT
        $this->set(compact('checklist', 'machines'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $checklist = $this->Checklists->get($id, [
            'contain' => [
                'Machines',
                'Categories.ComponentCodeCategories',
                'Categories' => function ($query) {
                    return $query->order(['order_category' => 'ASC']);
                },
                'Categories.SubCategories.Questions' => function ($query) {
                    return $query->order(['order_question' => 'ASC']);
                },
            ],
        ]);
        $countCategories = $this->Checklists->Categories->query()->where(['checklist_id' => $id])->count();
        if ($this->request->is(['patch', 'post', 'put'])) {
            $checklist = $this->Checklists->patchEntity($checklist, $this->request->getData());
            if ($this->Checklists->save($checklist)) {
                $this->Flash->success(__('Enregistrement réussi.'));

                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error(__('Une erreur est survenue.'));
        }
        $machines = $this->Checklists->Machines->find('list')
            ->contain(['Sectors']);
        $this->set(compact('checklist', 'machines', 'countCategories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $checklist = $this->Checklists->get($id);
        if ($checklist->parent_checklist_id) {
            $checklists = $this->Checklists->find()
                ->where([
                    'OR' => [
                        [
                            'Checklists.id' => $checklist->parent_checklist_id,
                        ],
                        [
                            'parent_checklist_id' => $checklist->parent_checklist_id,
                        ],
                    ],
                ]);

            foreach ($checklists as $checklist) {
                $this->Checklists->delete($checklist);
            }
        } else {
            $this->Checklists->delete($checklist);
        }
        $this->Flash->success(__('Enregistrement réussi.'));

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Duplicate method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function duplicate($id)
    {
        $idUser = $this->request->getAttribute('identity')['id'];
        $countVersionChecklist = $this->Checklists->query()
            ->find('withDeleted')
            ->where([
                'OR' => [
                    ['parent_checklist_id' => $id],
                    ['id' => $id],
                ],
            ])
            ->count();
        $countVersionChecklist++;
        $newChecklistEntity = $this->Checklists->duplicate($id);

        $this->Checklists->query()->update()
            ->set([
                'validated_at' => null,
                'parent_checklist_id' => $newChecklistEntity->parent_checklist_id ?: $id,
                'author_id' => $idUser,
                'version' => $countVersionChecklist,
            ])
            ->where(['id' => $newChecklistEntity->id])
            ->execute();

        return $this->redirect(['action' => 'edit', $newChecklistEntity->id]);
    }

    /**
     * Validate method
     *
     * @param string|null $id Checklist id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function validate()
    {
        $this->request->allowMethod(['post', 'delete']);

        $postData = $this->request->getData();
        $checklist = $this->Checklists->get($postData['id']);
        $queryUpdate = $this->Checklists->query()->update()
            ->set([
                'validated_at' => FrozenTime::now(),
            ])
            ->where(['id' => $checklist->id]);
        if ($postData['isValid'] ? $queryUpdate->execute() : $this->Checklists->delete($checklist)) {
            $this->Flash->success(__('Enregistrement réussi.'));

            return $this->redirect(['controller' => 'Checklists', 'action' => 'modifiedChecklists']);
        }
        $this->Flash->error(__('Une erreur est survenue.'));
    }
}
