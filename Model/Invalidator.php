<?php

namespace SomethingDigital\InvalidateAdminPasswords\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Backend\App\ConfigInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Email\Model\BackendTemplate;
use Magento\Store\Model\Store;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use SomethingDigital\InvalidateAdminPasswords\Model\Compatibility\MSPTwoFactorAuth;

class Invalidator
{
    /**
     * No string will hash to this value.
     */
    const INVALIDATED_PASSWORD_STRING = '-----------------------------';

    const XML_PATH_EMAIL_ENABLED = 'admin/emails/sd_invalidate_admin_passwords_send_email';

    const XML_PATH_EMAIL_TEMPLATE = 'admin/emails/sd_invalidate_admin_passwords_password_reset_required_template';

    const XML_PATH_CLEAR_MSP_TFA = 'admin/emails/sd_invalidate_admin_passwords_clear_msp_tfa';

    private $transportBuilder;

    private $config;

    private $state;

    private $userResource;

    private $userCollectionFactory;

    private $moduleManager;

    private $mspTFA;

    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigInterface $config,
        UserResource $userResource,
        UserCollectionFactory $userCollectionFactory,
        ModuleManager $moduleManager,
        MSPTwoFactorAuth $mspTFA
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->userResource = $userResource;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->moduleManager = $moduleManager;
        $this->mspTFA = $mspTFA;
    }

    public function invalidate()
    {
        /**
         * Invalidate all the passwords
         */
        $this->userResource->getConnection()->update(
            $this->userResource->getMainTable(),
            ['password' => self::INVALIDATED_PASSWORD_STRING]
        );

        if ($this->moduleManager->isEnabled('MSP_TwoFactorAuth') &&
            $this->config->getValue(self::XML_PATH_CLEAR_MSP_TFA)) {
            $this->mspTFA->execute();
        }

        if (!$this->config->getValue(self::XML_PATH_EMAIL_ENABLED)) {
            return true;
        }

        $collection = $this->userCollectionFactory->create();
        foreach ($collection as $user) {
            $this->notify($user);
        }

        return true;
    }

    /**
     * @see Magento\User\Model\Notificator::sendNotification()
     */
    private function notify($user)
    {
        /**
         * @see Magento\User\Model\Notificator::sendNotification()
         */
        $transport = $this->transportBuilder
            ->setTemplateIdentifier(
                $this->config->getValue(self::XML_PATH_EMAIL_TEMPLATE)
            )
            ->setTemplateModel(BackendTemplate::class)
            ->setTemplateOptions([
                'area' => FrontNameResolver::AREA_CODE,
                'store' => Store::DEFAULT_STORE_ID
            ])
            ->setTemplateVars([
                'user' => $user
            ])
            ->setFrom(
                $this->config->getValue('admin/emails/forgot_email_identity')
            )
            ->addTo(
                $user->getEmail(),
                $user->getFirstName() . ' ' . $user->getLastName()
            )
            ->getTransport();

        $transport->sendMessage();
    }
}
