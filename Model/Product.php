<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\VeniaCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Product
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;


     /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */

    protected $objectManager;
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->objectManager=$objectManager;
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

            foreach ($rows as $row) {
                $_productsArray[] = array_combine($header, $row);
            }
            $this->importerModel = $this->objectManager->create('FireGento\FastSimpleImport\Model\Importer');
            $this->importerModel->setImportImagesFileDir('vendor/magentoese/module-venia-media-sample-data/catalog/product');
            $this->importerModel->setValidationStrategy('validation-skip-errors');
            try {
                $this->importerModel->processImport($_productsArray);
            } catch (\Exception $e) {
                print_r($e->getMessage());
            }

            print_r($this->importerModel->getLogTrace());
            print_r($this->importerModel->getErrorMessages());
            unset ($_productsArray);
            unset ($this->importerModel);
        }

    }

    public function setFixtures(array $fixtures)
    {
        $this->fixtures = $fixtures;
        return $this;
    }

}
