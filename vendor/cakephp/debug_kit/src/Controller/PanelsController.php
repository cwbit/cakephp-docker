<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;

/**
 * Provides access to panel data.
 *
 * @property \DebugKit\Model\Table\PanelsTable $Panels
 */
class PanelsController extends DebugKitController
{
    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadComponent('RequestHandler');
    }

    /**
     * Before render handler.
     *
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     */
    public function beforeRender(EventInterface $event)
    {
        $this->viewBuilder()
            ->addHelpers([
                'Form', 'Html', 'Number', 'Url', 'DebugKit.Toolbar',
                'DebugKit.Credentials', 'DebugKit.SimpleGraph',
            ])
            ->setLayout('DebugKit.toolbar');

        if (!$this->request->is('json')) {
            $this->viewBuilder()->setClassName('DebugKit.Ajax');
        }
    }

    /**
     * Index method that lets you get requests by panelid.
     *
     * @param string $requestId Request id
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function index($requestId = null)
    {
        $query = $this->Panels->find('byRequest', ['requestId' => $requestId]);
        $panels = $query->toArray();
        if (empty($panels)) {
            throw new NotFoundException();
        }
        $this->set([
            'panels' => $panels,
        ]);
        $this->viewBuilder()->setOption('serialize', ['panels']);
    }

    /**
     * View a panel's data.
     *
     * @param string $id The id.
     * @return void
     */
    public function view($id = null)
    {
        $this->set('sort', $this->request->getCookie('debugKit_sort'));
        $panel = $this->Panels->get($id, ['contain' => ['Requests']]);

        $this->set('panel', $panel);
        // @codingStandardsIgnoreStart
        $this->set(@unserialize($panel->content));
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get Latest request history panel
     *
     * @return \Cake\Http\Response|null
     */
    public function latestHistory()
    {
        /** @var array{id:string}|null $request */
        $request = $this->Panels->Requests->find('recent')
            ->select(['id'])
            ->disableHydration()
            ->first();
        if (!$request) {
            throw new NotFoundException('No requests found');
        }
        /** @var array{id:string}|null $historyPanel */
        $historyPanel = $this->Panels->find('byRequest', ['requestId' => $request['id']])
            ->where(['title' => 'History'])
            ->select(['id'])
            ->first();
        if (!$historyPanel) {
            throw new NotFoundException('History Panel from latest request not found');
        }

        return $this->redirect([
            'action' => 'view', $historyPanel['id'],
        ]);
    }
}
