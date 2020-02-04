<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\VeniaCatalogSampleData\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;

class updateTopsCategoryLayout implements DataPatchInterface,PatchVersionInterface
{
   /** @var CollectionFactory  */
    protected $categoryCollectionFactory;

    /** @var CategoryRepositoryInterface  */
    protected $categoryRepository;


    /** @var CategoryFactory  */
    protected $categoryFactory;

    /**
     * updateTopsCategoryLayout constructor.
     * @param CollectionFactory $categoryCollectionFactory
     * @param CategoryRepositoryInterface $repository
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
     CollectionFactory $categoryCollectionFactory,
     CategoryRepositoryInterface $repository,
     CategoryFactory $categoryFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $repository;
        $this->categoryFactory = $categoryFactory;
    }

    public function apply()
    {
        $collection = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToFilter('url_key',array('eq' => 'venia-tops'));

        if ($collection->getSize()) {
            $categoryId = $collection->getFirstItem()->getId();
        }
        $currentCategory = $this->categoryFactory->create()->setStoreId(0)->load($categoryId);

        $currentCategory->setCustomLayoutUpdateFile('VeniaCategory');
        $this->categoryRepository->save($currentCategory);
    }

    public static function getDependencies()
    {
        return [OldInstallData::class,OldUpgradeData::class];
    }

    public static function getVersion()
    {
        return '0.0.2';
    }

    public function getAliases()
    {
        return [];
    }
}
