<?php

namespace SomethingDigital\InvalidateAdminPasswords\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Backend\App\ConfigInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Email\Model\BackendTemplate;
use Magento\Store\Model\Store;

class Invalidator
{
    private $transportBuilder;

    private $config;

    private $state;

    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigInterface $config
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
    }

    public function invalidate()
    {
        /**
         * @see Magento\User\Model\Notificator::sendNotification()
         */
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('sd_invalidate_admin_passwords_password_reset_required')
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

        echo 'DONE' . PHP_EOL;
    }
}
