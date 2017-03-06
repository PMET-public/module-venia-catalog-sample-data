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
     * Upsells
     *
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Upsells
     */
    protected $upsells;


    /**
     * App State
     *
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * @var \Magento\CatalogRuleSampleData\Model\Rule
     */
    protected $catalogRule;
    /**
     * @var \Magento\SalesRuleSampleData\Model\Rule
     */
    protected $salesRule;

    /**
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Review
     */
    protected $review;
    /**
     * @var \MagentoEse\VeniaCatalogSampleData\Model\Video
     */
    protected $video;

    /**
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Category $categorySetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Attribute $attributeSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Product $productSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Swatches $swatchesSetup
     * @param \MagentoEse\VeniaCatalogSampleData\Model\LumaSuppression $lumaSuppression
     * @param \Magento\Framework\App\State $state
     * @param \Magento\CatalogRuleSampleData\Model\Rule $catalogRule
     * @param \Magento\SalesRuleSampleData\Model\Rule $salesRule
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Upsells $upsells
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Review $review
     * @param \MagentoEse\VeniaCatalogSampleData\Model\Video $video
     * @param \Magento\Indexer\Model\Processor $index
     */


    public function __construct(
        \MagentoEse\VeniaCatalogSampleData\Model\Category $categorySetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Attribute $attributeSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Product $productSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\Swatches $swatchesSetup,
        \MagentoEse\VeniaCatalogSampleData\Model\LumaSuppression $lumaSuppression,
        \Magento\Framework\App\State $state,
        \Magento\CatalogRuleSampleData\Model\Rule $catalogRule,
        \Magento\SalesRuleSampleData\Model\Rule $salesRule,
        \MagentoEse\VeniaCatalogSampleData\Model\Upsells $upsells,
        \MagentoEse\VeniaCatalogSampleData\Model\Review $review,
        \MagentoEse\VeniaCatalogSampleData\Model\Video $video,
        \Magento\Indexer\Model\Processor $index
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
        $this->productSetup = $productSetup;
        $this->swatchesSetup = $swatchesSetup;
        $this->lumaSuppression = $lumaSuppression;
        $this->catalogRule  = $catalogRule;
        $this->salesRule = $salesRule;
        $this->upsells = $upsells;
        $this->review = $review;
        $this->video = $video;
        $this->index = $index;
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
        //add catalog promos
        $this->catalogRule->install(['MagentoEse_VeniaCatalogSampleData::fixtures/catalogRules.csv']);
        //add cart promos
        $this->salesRule->install(['MagentoEse_VeniaCatalogSampleData::fixtures/salesRules.csv']);
        //add upsells
        $this->upsells->install(['MagentoEse_VeniaCatalogSampleData::fixtures/upsells.csv']);
        //add reviews
        $this->review->install(['MagentoEse_VeniaCatalogSampleData::fixtures/reviews.csv']);
        //add video
        $this->video->install(['MagentoEse_VeniaCatalogSampleData::fixtures/veniaVideo.csv']);
        //reIndex as MECE redeploy will not automatically reindex
        $this->index->reindexAll();

    }
}