<?php

// See dev/tests/integration/testsuite/Magento/User/_files/dummy_user.php

\Magento\TestFramework\Helper\Bootstrap::getInstance();
$user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\User\Model\User::class);
$user->setFirstname(
    'Dummy'
)->setLastname(
    'Dummy'
)->setEmail(
    'dummy@dummy.com'
)->setUsername(
    'dummy_username'
)->setPassword(
    'dummy_password1'
)->save();
