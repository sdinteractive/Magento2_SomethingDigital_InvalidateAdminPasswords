<?php

namespace SomethingDigital\InvalidateAdminPasswords\Test\Integration\Model;

use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use Magento\Framework\App\State;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use SomethingDigital\InvalidateAdminPasswords\Model\Invalidator;

class InvalidatorTest extends TestCase
{
    private $invalidator;

    private $userCollectionFactory;

    private $transportBuilderMock;

    protected function setUp()
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->userCollectionFactory = $objectManager->create(UserCollectionFactory::class);

        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);

        $this->transportBuilderMock->method('setTemplateIdentifier')
            ->willReturn($this->transportBuilderMock);

        $this->transportBuilderMock->method('setTemplateModel')
            ->willReturn($this->transportBuilderMock);

        $this->transportBuilderMock->method('setTemplateOptions')
            ->willReturn($this->transportBuilderMock);

        $this->transportBuilderMock->method('setTemplateVars')
            ->willReturn($this->transportBuilderMock);

        $this->transportBuilderMock->method('setFrom')
            ->willReturn($this->transportBuilderMock);

        $this->transportBuilderMock->method('addTo')
            ->willReturn($this->transportBuilderMock);

        $this->transportBuilderMock->method('getTransport')
            ->willReturn(new \Magento\TestFramework\Mail\TransportInterfaceMock());

        $this->invalidator = $objectManager->create(
            Invalidator::class,
            [
                'transportBuilder' => $this->transportBuilderMock
            ]
        );
    }

    /**
     * @magentoDataFixture createAdminUser
     */
    public function testInvalidate()
    {
        $this->transportBuilderMock->expects($this->atLeastOnce())
            ->method('setTemplateIdentifier');

        $this->invalidator->invalidate();
        $userCollection = $this->userCollectionFactory->create();
        foreach ($userCollection as $user) {
            $this->assertEquals($user->getPassword(), Invalidator::INVALIDATED_PASSWORD_STRING);
        }
    }

    /**
     * @magentoDataFixture createAdminUser
     * @magentoAdminConfigFixture admin/emails/sd_invalidate_admin_passwords_send_email 0
     */
    public function testInvalidateWithSendEmailOff()
    {
        $this->transportBuilderMock->expects($this->never())
            ->method('setTemplateIdentifier');

        $this->invalidator->invalidate();
    }

    public static function createAdminUser()
    {
        include __DIR__ . '/../_files/create_admin_user.php';
    }
}
