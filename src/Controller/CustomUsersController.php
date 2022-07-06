<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Utility\Hash;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $CustomUsers
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CustomUsersController extends AppController
{
    /**
     * After login, choose where to redirect the user.
     *
     * @return \App\Controller\Redirect
     */
    public function postLoginRedirect()
    {
        return $this->redirect('/');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $users = $this->paginate($this->CustomUsers);
        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view($id)
    {
        $user = $this->CustomUsers->get($id);
        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function createUser()
    {
        $roles = Configure::read('role_users');
        $user = $this->CustomUsers->newEmptyEntity();

        if ($this->request->is('post')) {
            $dataForm = $this->request->getData();

            $options = [
                'token_expiration' => Configure::read('Users.Token.expiration'),
                'validate_email' => (bool)Configure::read('Users.Email.validate'),
                'use_tos' => (bool)Configure::read('Users.Tos.required'),
                'tos' => true,
            ];

            $tokenExpiration = Hash::get($options, 'token_expiration');
            $user->updateToken($tokenExpiration);

            $dataForm['active'] = true;
            $dataForm['activation_date'] = new FrozenTime();

            $saveOptions = [
                'accessibleFields' => ['role' => true],
                'associated' => ['UserSectors'],
            ];

            $dataForm['user_sectors'] = $this->_rejectUncheckedSectors($dataForm['user_sectors'] ?? []);

            $user = $this->CustomUsers->patchEntity(
                $user,
                $dataForm,
                $saveOptions
            );

            if ($this->CustomUsers->save($user, $saveOptions)) {
                $this->Flash->success(__('Merci pour votre inscription, votre compte a été activé.'));

                return $this->redirect($this->referer());
            }

            $this->Flash->error(__('Une erreur est survenue. Veuillez réessayer.'));
        }

        $userSectors = $this->CustomUsers->UserSectors->Sectors
            ->find('list')
            ->order(['sector_name'])
            ->toArray();

        $this->set(compact('user', 'roles', 'userSectors'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return mixed Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id)
    {
        $user = $this->CustomUsers->get($id, ['contain' => ['UserSectors']]);
        $roles = Configure::read('role_users');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $dataForm = $this->request->getData();
            $dataForm['user_sectors'] = $this->_rejectUncheckedSectors($dataForm['user_sectors'] ?? []);

            $saveOptions = [
                'accessibleFields' => ['role' => true],
                'associated' => ['UserSectors'],
            ];
            $user = $this->CustomUsers->patchEntity($user, $dataForm, $saveOptions);
            if ($this->CustomUsers->save($user, $saveOptions)) {
                $this->Flash->success('Enregistrement réussi.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Une erreur est survenue.');
        }

        $userSectors = $this->CustomUsers->UserSectors->Sectors
            ->find('list')
            ->order(['sector_name'])
            ->toArray();
        $associatedSectorIds = collection($user->user_sectors ?? [])
            ->extract('sector_id')
            ->toArray();

        $this->set(compact('user', 'userSectors', 'associatedSectorIds', 'roles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response Redirects to index.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function delete($id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->CustomUsers->get($id);
        if ($this->CustomUsers->delete($user)) {
            $this->Flash->success(__('Enregistrement réussi.'));
        } else {
            $this->Flash->error(__('Une erreur est survenue.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    protected function _rejectUncheckedSectors($values)
    {
            return collection($values ?? [])
                ->reject(function ($value) {
                    return $value['sector_id'] === '0';
                })
                ->toArray();
    }
}
