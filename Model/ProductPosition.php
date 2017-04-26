<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\VeniaCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class ProductPosition
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $objectManager;

    /**
     * @var SampleDataContext
     */
    protected $sampleDataContext;

    /**
     * @var \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    protected $resourceConnection;


    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $productFactory;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     */
    protected $categoryCollection;

     /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
      * @param \Magento\Framework\App\ResourceConnection $resourceConnection
      * @param \Magento\Catalog\Model\ProductFactory $productFactory
      * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     */

    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->objectManager=$objectManager;
        $this->resourceConnection = $resourceConnection;
        $this->productFactory = $productFactory;
        $this->categoryCollection = $categoryCollection;
    }

    /**
     * @param array $productFixtures
     * @throws \Exception
     */
    public function install(array $productFixtures)
    {
        foreach ($productFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);
            //set position of product in collection category
            $categoryPrefix='Shop The Look/';


            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $categoryPath = $categoryPrefix.$data['collection'];
                $categoryIds[] = $this->getIdFromPath($this->_initCategories(),$categoryPath);
                foreach($categoryIds as $categoryId) {
                    $product = $this->productFactory->create();
                    $productId = $product->getIdBySku($data['sku']);
                    $this->updateProductPosition($categoryId,$productId,$data['position']);
                }
            }
        }

    }

    private function updateProductPosition($categoryId,$productId,$position){
        //this is not the proper method, but was done in interest of deadline
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('catalog_category_product');
        $sql = "update " . $tableName . " set position = ".$position." where category_id = ".$categoryId." and product_id=".$productId;
        $connection->query($sql);
    }

    protected function getIdFromPath($categories,$string)
    {
        if (in_array($string, array_keys($categories))) {
            return $categories[$string];
        }

        return false;
    }

    protected function _initCategories()
    {
        $collection = $this->categoryCollection->create();
        $collection->addNameToResult();
        $categories = array();
        $categoriesWithRoots = array();
        foreach ($collection as $category) {
            $structure = explode('/', $category->getPath());
            $pathSize = count($structure);
            if ($pathSize > 1) {
                $path = array();
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }
                $rootCategoryName = array_shift($path);
                if (!isset($categoriesWithRoots[$rootCategoryName])) {
                    $categoriesWithRoots[$rootCategoryName] = array();
                }
                $index = implode('/', $path);
                $categoriesWithRoots[$rootCategoryName][$index] = $category->getId();
                if ($pathSize > 2) {
                    $categories[$index] = $category->getId();
                }
            }
        }
        return $categories;
    }

}
