<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogStagingGraphQl\Model\Plugin;

use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\CategoryTree;
use Magento\CatalogStaging\Model\Indexer\Category\Product\PreviewReindex;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Staging\Model\VersionManager;

class PreviewReindexPlugin
{
    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * @var PreviewReindex
     */
    private $previewReindex;

    /**
     * @param VersionManager $versionManager
     * @param PreviewReindex $previewReindex
     */
    public function __construct(
        VersionManager $versionManager,
        PreviewReindex $previewReindex
    ) {
        $this->versionManager = $versionManager;
        $this->previewReindex = $previewReindex;
    }

    /**
     * @param CategoryTree $subject
     * @param ResolveInfo $resolveInfo
     * @param int $rootCategoryId
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetTree(
        CategoryTree $subject,
        ResolveInfo $resolveInfo,
        int $rootCategoryId,
        int $storeId
    ): void {
        if ($this->versionManager->isPreviewVersion()) {
            $this->previewReindex->reindex($rootCategoryId, $storeId);
        }
    }
}
