<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\VeniaCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Catalog\Model\ResourceModel\Product\Gallery as GalleryResource;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Api\Data\ProductInterface;
/**
 * Setup sample attributes
 *
 * Class Attribute
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Video
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param GalleryResource $galleryResource
     * @param \Magento\ProductVideo\Model\ResourceModel\Video $videoResourceModel
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        GalleryResource $galleryResource,
        \Magento\ProductVideo\Model\ResourceModel\Video $videoResourceModel,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory

    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->galleryResource = $galleryResource;
        $this->videoResourceModel = $videoResourceModel;
        $this->eavConfig = $eavConfig;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
    }

    /**
     * @param array $fixtures
     * @throws \Exception
     */
    public function install(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;
                $productId = $this->getProductIdBySku($row['sku']);
                $linkField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();
                $mediaAttribute = $this->eavConfig->getAttribute('catalog_product', 'media_gallery');

                $id = $this->galleryResource->insertGallery([
                    'attribute_id' => $mediaAttribute->getAttributeId(),
                    "media_type" => \Magento\ProductVideo\Model\Product\Attribute\Media\ExternalVideoEntryConverter::MEDIA_TYPE_CODE,
                    "value" => $row['image']

                ]);
                //INSERT INTO `catalog_product_entity_media_gallery` (`attribute_id`, `value`, `media_type`) VALUES ('90', '/V/D/VD11-LY_main.jpg', 'external-video')

                $this->galleryResource->bindValueToEntity($id, $productId);
                // INSERT INTO `catalog_product_entity_media_gallery_value_to_entity` (`value_id`,`row_id`) VALUES ('5200', '3190') ON DUPLICATE KEY UPDATE `value_id` = VALUES(`value_id`), `row_id` = VALUES(`row_id`)


                $this->galleryResource->insertGalleryValueInStore([
                    'value_id' => $id,
                    'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    $linkField => $productId,
                    'label' => 'Video',
                    'position' => 4
                ]);
                //INSERT INTO `catalog_product_entity_media_gallery_value` (`value_id`, `store_id`, `row_id`, `label`, `position`) VALUES ('5200', '0', '3190', 'Video', '4')


                $this->videoResourceModel->insertOnDuplicate([
                    "value_id" => $id,
                    "store_id" => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    "url" => $row['video'],
                    "title" => $row['name']
                ]);
                //INSERT INTO `catalog_product_entity_media_gallery_value_video` (`value_id`,`store_id`,`url`,`title`) VALUES ('5200', '0', 'https://vimeo.com/196467074', 'Athena Tank Dress') ON DUPLICATE KEY UPDATE `value_id` = VALUES(`value_id`), `store_id` = VALUES(`store_id`), `url` = VALUES(`url`), `title` = VALUES(`title`)


            }
        }

    }

    /**
     * @deprecated
     *
     * @return \Magento\Framework\EntityManager\MetadataPool|mixed
     */
    private function getMetadataPool()
    {
        if (!($this->metadataPool)) {
            return ObjectManager::getInstance()->get(
                '\Magento\Framework\EntityManager\MetadataPool'
            );
        } else {
            return $this->metadataPool;
        }
    }

    /**
     * Retrieve product ID by sku
     *
     * @param string $sku
     * @return int|null
     */
    protected function getProductIdBySku($sku)
    {
        if (empty($this->productIds)) {
            foreach ($this->productCollection as $product) {
                $this->productIds[$product->getSku()] = $product->getId();
            }
        }
        if (isset($this->productIds[$sku])) {
            return $this->productIds[$sku];
        }
        return null;
    }

}
