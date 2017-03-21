<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\VeniaCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Category
 */
class Category
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Catalog\Api\Data\CategoryInterfaceFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\TreeFactory
     */
    protected $resourceCategoryTreeFactory;


    /**
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $categoryTree;

    /**
     * @var \Magento\Store\Api\Data\StoreInterfaceFactory
     */
    protected $storeFactory;
    /**
     * @var \Magento\CMS\Api\BlockRepositoryInterface
     */
    protected $blockRepository;
    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $resourceCategoryTreeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\Data\StoreInterfaceFactory $storeFactory
     * @param \Magento\CMS\Api\BlockRepositoryInterface $blockRepository
     */


    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $resourceCategoryTreeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\Data\StoreInterfaceFactory $storeFactory,
        \Magento\CMS\Api\BlockRepositoryInterface $blockRepository
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->categoryFactory = $categoryFactory;
        $this->resourceCategoryTreeFactory = $resourceCategoryTreeFactory;
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
        $this->blockRepository = $blockRepository;
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
                $this->createCategory($data);
            }
        }
    }

    /**
     * @param array $row
     * @param \Magento\Catalog\Model\Category $category
     * @return void
     */
    protected function setAdditionalData($row, $category)
    {
        $additionalAttributes = [
            'position',
            'display_mode',
            'page_layout',
            'custom_layout_update',
            'look_book_main_image',
            'display_mode',
            'description'
        ];

        foreach ($additionalAttributes as $categoryAttribute) {
            if (!empty($row[$categoryAttribute])) {
                if($categoryAttribute=='landing_page'){
                    try {
                        $row[$categoryAttribute] = $this->blockRepository->getById($row[$categoryAttribute])->getId();
                    }catch(\Exception $e){
                        //ignore if block doesn't exist
                    }
                }
                $attributeData = [$categoryAttribute => $row[$categoryAttribute]];
                $category->addData($attributeData);
            }
        }
    }

    /**
     * Get category name by path
     *
     * @param string $path
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getCategoryByPath($path)
    {
        $store = $this->storeFactory->create();
        $store->load('venia_us');
        $rootCatId = $store->getGroup()->getDefaultStore()->getRootCategoryId();
        $names = array_filter(explode('/', $path));
        $tree = $this->getTree($rootCatId);
        foreach ($names as $name) {
            $tree = $this->findTreeChild($tree, $name);
            if (!$tree) {
                $tree = $this->findTreeChild($this->getTree($rootCatId, true), $name);
            }
            if (!$tree) {
                break;
            }
        }
        return $tree;
    }

    /**
     * Get child categories
     *
     * @param \Magento\Framework\Data\Tree\Node $tree
     * @param string $name
     * @return mixed
     */
    protected function findTreeChild($tree, $name)
    {
        $foundChild = null;
        if ($name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $foundChild = $child;
                    break;
                }
            }
        }
        return $foundChild;
    }

    /**
     * Get category tree
     *
     * @param int|null $rootNode
     * @param bool $reload
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getTree($rootNode = null, $reload = false)
    {
        if (!$this->categoryTree || $reload) {
            if ($rootNode === null) {
                $rootNode = $this->storeManager->getDefaultStoreView()->getRootCategoryId();
            }

            $tree = $this->resourceCategoryTreeFactory->create();
            $node = $tree->loadNode($rootNode)->loadChildren();

            $tree->addCollectionData(null, false, $rootNode);

            $this->categoryTree = $node;
        }
        return $this->categoryTree;
    }

    /**
     * @param array $row
     * @return void
     */
    protected function createCategory($row)
    {
        $category = $this->getCategoryByPath($row['path'] . '/' . $row['name']);
        if (!$category) {
            $parentCategory = $this->getCategoryByPath($row['path']);
            $data = [
                'parent_id' => $parentCategory->getId(),
                'name' => $row['name'],
                'is_active' => $row['active'],
                'is_anchor' => $row['is_anchor'],
                'include_in_menu' => $row['include_in_menu'],
                'url_key' => $row['url_key'],
                'store_id' => 0
            ];
            $category = $this->categoryFactory->create();
            $category->setData($data)
                ->setPath($parentCategory->getData('path'))
                ->setAttributeSetId($category->getDefaultAttributeSetId());
            $this->setAdditionalData($row, $category);
            $category->save();
        }
    }

}
