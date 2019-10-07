<?php

namespace SomethingDigital\InvalidateAdminPasswords\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Backend\App\ConfigInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Email\Model\BackendTemplate;
use Magento\Store\Model\Store;
use Magento\User\Model\ResourceModel\User as UserResource;

class Invalidator
{
    /**
     * No string will hash to this value.
     */
    const INVALIDATED_PASSWORD_STRING = '-----------------------------';

    const XML_PATH_EMAIL_ENABLED = 'admin/emails/sd_invalidate_admin_passwords_send_email';

    private $transportBuilder;

    private $config;

    private $state;

    private $userResource;

    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigInterface $config,
        UserResource $userResource
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->userResource = $userResource;
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

        if (!$this->config->getValue(self::XML_PATH_EMAIL_ENABLED)) {
            return true;
        }

        /**
         * @see Magento\User\Model\Notificator::sendNotification()
         */
        $transport = $this->transportBuilder
            ->setTemplateIdentifier(
                $this->config->getValue('admin/emails/sd_invalidate_admin_passwords_password_reset_required_template')
            )
            ->setTemplateModel(BackendTemplate::class)
            ->setTemplateOptions([
                'area' => FrontNameResolver::AREA_CODE,
                'store' => Store::DEFAULT_STORE_ID
            ])
            ->setTemplateVars([])
            ->setFrom(
                $this->config->getValue('admin/emails/forgot_email_identity')
            )
            ->addTo('test@example.com')
            ->getTransport();

        $transport->sendMessage();

        return true;
    }
}
