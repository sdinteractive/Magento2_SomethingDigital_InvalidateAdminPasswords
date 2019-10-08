<?php

namespace SomethingDigital\InvalidateAdminPasswords\Model\Compatibility;

use Magento\Framework\ObjectManagerInterface;

class MSPTwoFactorAuth
{
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function execute()
    {
        /**
         * We can't use constructor DI as the class may not exist.
         */
        $userConfigResource = $this->objectManager->create('MSP\TwoFactorAuth\Model\ResourceModel\UserConfig');
        $userConfigResource->getConnection()->delete(
            $userConfigResource->getMainTable()
        );
    }
}
