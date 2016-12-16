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
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Category $categorySetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Attribute $attributeSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Product $productSetup
     */


    public function __construct(
        \MagentoEse\VeniaCatalogSampleData\Model\Category $categorySetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Attribute $attributeSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Product $productSetup
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
        $this->productSetup = $productSetup;

    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        //$this->attributeSetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/attributes.csv']);
        $this->categorySetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/categories.csv']);
        //$this->productSetup->install(['MagentoEse_VeniaCatalogSampleData::fixtures/milwaukee_products_1.csv']);
    }
}