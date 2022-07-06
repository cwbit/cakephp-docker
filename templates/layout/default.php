<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

?>
<!DOCTYPE html>
<html>

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>
    <?php // echo $this->Html->css(['normalize.min', 'milligram.min', 'cake'])
    ?>
    <?= $this->AssetMix->css('app') ?>

</head>

<body style="background-image: url('/img/bg_01.jpg');">

    <?= $this->element('global/navbar') ?>

    <main class="main">
        <div class="container shadow p-3 my-5 bg-white">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <?= $this->element('global/footer') ?>

    <?= $this->AssetMix->script('app') ?>
    <?= $this->fetch('script') ?>
</body>

</html>
