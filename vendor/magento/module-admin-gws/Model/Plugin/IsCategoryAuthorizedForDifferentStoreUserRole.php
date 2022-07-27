<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdminGws\Model\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category\Authorization;
use Magento\Framework\Exception\AuthorizationException;
use Magento\User\Model\UserFactory;

/**
 * Plugin for authorization of category changes for different store user role
 */
class IsCategoryAuthorizedForDifferentStoreUserRole
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * Factory class for user model
     *
     * @var UserFactory
     */
    private $userFactory;

    /**
     * Initialize dependencies
     *
     * @param UserContextInterface $userContext
     * @param UserFactory $userFactory
     */
    public function __construct(
        UserContextInterface $userContext,
        UserFactory $userFactory
    ) {
        $this->userContext = $userContext;
        $this->userFactory = $userFactory;
    }

    /**
     * Check if the current admin user have access to the category current store
     *
     * @param Authorization $subject
     * @param CategoryInterface $category
     * @throws AuthorizationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAuthorizeSavingOf(
        Authorization $subject,
        CategoryInterface $category
    ): void {
        if ($this->userContext->getUserId()
            && $this->userContext->getUserType() === UserContextInterface::USER_TYPE_ADMIN
        ) {
            $currentAdminId = $this->userContext->getUserId();
            $adminUser = $this->userFactory->create()->load($currentAdminId);
            $adminUserRole = $adminUser->getRole();
            $isGwsRoleForAll = $adminUserRole->getGwsIsAll();
            $adminUserAssignedStores= $adminUserRole->getGwsStores();
            $currentCategoryStoreId = $category->getStoreId();
            if (!$isGwsRoleForAll && !empty($adminUserAssignedStores) &&
                !in_array($currentCategoryStoreId, $adminUserAssignedStores)) {
                throw new AuthorizationException(__('Not allowed to edit the category\'s design attributes'));
            }
        }
    }
}
