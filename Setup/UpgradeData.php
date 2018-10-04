<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\VeniaCatalogSampleData\Setup;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Magento\Eav\Setup\EavSetup;


class UpgradeData implements UpgradeDataInterface
{


    public function __construct(State $state, EavSetup $eavSetup, ProductAttributeRepositoryInterface $productAttributeRepository)
    {
        try{
            $state->setAreaCode('adminhtml');
        }
        catch(\Magento\Framework\Exception\LocalizedException $e){
            // left empty
        }
        $this->eavSetup = $eavSetup;
        $this->productAttributeRepository = $productAttributeRepository;

    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.0.2', '<=')) {
            $attribute = $this->productAttributeRepository->get('video_file');
            $attribute->setIsSearchable('false');
            $attribute->setIsComparable('false');
            $attribute->setIsVisibleInAdvancedSearch('false');
            $attribute->setIsFilterable(false);
            $attribute->setIsFilterableInSearch(false);
            $this->productAttributeRepository->save($attribute);
        }




        $setup->endSetup();
    }
}
