<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\VeniaCatalogSampleData\Setup\Patch\Data;


use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\App\State;
use Magento\Eav\Setup\EavSetup;

class OldUpgradeData implements DataPatchInterface,PatchVersionInterface
{

    /** @var EavSetup  */
    protected $eavSetup;

    /** @var ProductAttributeRepositoryInterface  */
    protected $productAttributeRepository;

    /**
     * OldUpgradeData constructor.
     * @param State $state
     * @param EavSetup $eavSetup
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */

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

    public function apply()
    {
        $attribute = $this->productAttributeRepository->get('video_file');
        $attribute->setIsComparable('false');
        $attribute->setIsFilterable(false);
        $attribute->setIsFilterableInSearch(false);
        $this->productAttributeRepository->save($attribute);
    }

    public static function getDependencies()
    {
        return [OldInstallData::class];
    }

    public function getAliases()
    {
        return [];
    }

    public static function getVersion()
    {
       return '0.0.2';
    }
}
