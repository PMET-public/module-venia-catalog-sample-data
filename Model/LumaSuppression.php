<?php

namespace MagentoEse\VeniaCatalogSampleData\Model;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;



class LumaSuppression
{

    /**
     * @var \Magento\Framework\Setup\SampleData\Context
     */

    protected $sampleDataContext;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $storeView;

    /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * #param \Magento\Store\Model\Store $storeView
     **/

    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\Store $storeView,
        \Magento\Indexer\Model\Processor $index

    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->productFactory = $productFactory;
        $this->storeView = $storeView;
        $this->index = $index;

    }
    public function install(array $productFixtures){

        //Index needs to be run before setting products in the new store
        $this->index->reindexAll();
        //get sku,store,visibility from csv
        foreach ($productFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $_rows = $this->csvReader->getData($fileName);
            $_header = array_shift($_rows);

            foreach ($_rows as $_row) {

                $_product = $this->productFactory->create();
                $_data = [];
                foreach ($_row as $_key => $_value) {
                    $_data[$_header[$_key]] = $_value;
                }
                $_row = $_data;
                $_viewId = $this->storeView->load($_row['store_view_code'])->getStoreId();
                $_product->load($_product->getIdBySku($_row['sku']));
                $_product->setStoreId($_viewId);
                $_product->setVisibility($_row['visibility']);
                try {
                    $_product->save();
                }catch (Exception $e){
                    echo $_row['sku'] . "Failed\n";
                }
                unset($_product);

            }
        }
    }

}