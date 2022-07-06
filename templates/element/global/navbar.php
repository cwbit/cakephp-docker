<header>
    <nav class="navbar navbar-expand-md bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $this->Url->build('/') ?>">
                <?= $this->Html->image('logo_goodyear.svg', ['width' => '150px']) ?>

            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                <ul class="navbar-nav text-end">
                    <li class="nav-item">
                        <?= $this->AuthLink->link(
                            __('Gestion des utilisateurs'),
                            [
                                'controller' => 'CustomUsers',
                                'action' => 'index',
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'nav-link',
                                'escape' => false
                            ]
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= $this->AuthLink->link(
                            __('Secteurs'),
                            [
                                'controller' => 'Sectors',
                                'action' => 'index',
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'nav-link',
                                'escape' => false
                            ]
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= $this->AuthLink->link(
                            __('Machines'),
                            [
                                'controller' => 'Machines',
                                'action' => 'index',
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'nav-link',
                                'escape' => false
                            ]
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= $this->AuthLink->link(
                            __('Checklist'),
                            [
                                'controller' => 'Checklists',
                                'action' => 'index',
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'nav-link',
                                'escape' => false
                            ]
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= $this->AuthLink->link(
                            __('Contrôles'),
                            [
                                'controller' => 'Controls',
                                'action' => 'index',
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'nav-link',
                                'escape' => false
                            ]
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= $this->AuthLink->link(
                            __('Se connecter'),
                            [
                                'controller' => 'login',
                                'plugin' => false,
                                'prefix' => false,
                            ],
                            [
                                'class' => 'nav-link',
                                'escape' => false
                            ]
                        ) ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
