<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\VeniaCatalogSampleData\Setup;

use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * Setup class for category
     *
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Category
     */
    protected $categorySetup;

    /**
     * Setup class for product attributes
     *
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Attribute
     */
    protected $attributeSetup;

    /**
     * Setup class for products
     *
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Product
     */
    protected $productSetup;

    /**
     * Convert fashion_color and fashion_size attribute to swatches
     *
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Swatches
     */
    protected $swatchesSetup;

    /**
     * Suppress downloadable and bundled Luma products from Venia
     *
     * @var \MagentoEse\VeniaCatalogSampleData\Model\LumaSuppression
     */
    protected $lumaSuppression;


    /**
     * App State
     *
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Category $categorySetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Attribute $attributeSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Product $productSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Swatches $swatchesSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\LumaSuppression $lumaSuppression
     * @param \Magento\Framework\App\State $state
     */


    public function __construct(
        \MagentoEse\VeniaCatalogSampleData\Model\Category $categorySetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Attribute $attributeSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Product $productSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Swatches $swatchesSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\LumaSuppression $lumaSuppression,
        \Magento\Framework\App\State $state
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
        $this->productSetup = $productSetup;
        $this->swatchesSetup = $swatchesSetup;
        $this->lumaSuppression = $lumaSuppression;
        try{
            $state->setAreaCode('adminhtml');
        }
        catch(\Magento\Framework\Exception\LocalizedException $e){
            // left empty
        }

    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        //add attributes
        $this->attributeSetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/attributes.csv']);
        //set up text and color swatches
        $this->swatchesSetup->install();
        //add categories
        $this->categorySetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/categories.csv']);
        //suppress most luma products from venia store
        $this->productSetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/suppressLumaProductsFromVenia.csv']);
        //suppress luma bundle and downloadable products from venia. These cannot be done via import
        $this->lumaSuppression->install(['MagentoEse_VeniaCatalogSampleData::fixtures/suppressAdditionalLumaProductsFromVenia.csv']);
        //add venia products
        $this->productSetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/veniaProducts.csv']);


    }
}