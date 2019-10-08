<?php

namespace SomethingDigital\InvalidateAdminPasswords\Test\Integration\Model;

use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use Magento\Framework\App\State;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use SomethingDigital\InvalidateAdminPasswords\Model\Invalidator;

class InvalidatorTest extends TestCase
{
    private $invalidator;

    private $userCollectionFactory;

    protected function setUp()
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->invalidator = $objectManager->create(Invalidator::class);
        $this->userCollectionFactory = $objectManager->create(UserCollectionFactory::class);
    }

    /**
     * @magentoDataFixture createAdminUser
     */
    public function testInvalidate()
    {
        $this->invalidator->invalidate();
        $userCollection = $this->userCollectionFactory->create();
        foreach ($userCollection as $user) {
            $this->assertEquals($user->getPassword(), Invalidator::INVALIDATED_PASSWORD_STRING);
        }
    }

    public static function createAdminUser()
    {
        include __DIR__ . '/../_files/create_admin_user.php';
    }
}
