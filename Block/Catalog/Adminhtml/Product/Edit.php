<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer edit block
 *
 * @author  Imagine 2016 Hackathon
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
namespace MagentoHackathon\AdminProductStatus\Block\Catalog\Adminhtml\Product;


class Edit extends \Magento\Catalog\Block\Adminhtml\Product\Edit
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit.phtml';
    protected $_productRepository = null;
    protected $_stockRegistry = null;
    protected $_productInStoreScope = null;
    protected $_indexStatus = null;
    protected $_neededIndexes = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \MagentoHackathon\AdminProductStatus\Model\ResourceModel\Index\StatusInterface $indexStatus
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \MagentoHackathon\AdminProductStatus\Model\ResourceModel\Index\StatusInterface $indexStatus,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->_stockRegistry = $stockRegistry;
        $this->_indexStatus = $indexStatus;
        parent::__construct($context, $jsonEncoder, $attributeSetFactory, $registry, $productHelper, $data);
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     * @throws Exception
     */
    protected function _getProductInStoreScope($id){
        $store = $this->getStoreScope();
        if (!$store){
            throw new Exception("No store scope available.");
        }
        if ($this->_productInStoreScope == null) {
            $this->_productInStoreScope = $this->_productRepository->getById($id, false, $store->getId());
        }
        return $this->_productInStoreScope;
    }

    /**
     * @return bool|\Magento\Store\Api\Data\StoreInterface|null
     */
    public function getStoreScope(){
        $store = false;
        if ($this->_storeManager->hasSingleStore()){
            $store = $this->_storeManager->getDefaultStoreView();
        } else if ($storeId = $this->getRequest()->getParam('store')) {
            $store = $this->_storeManager->getStore($storeId);
        }
        return $store;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function isVisibleOnFrontend(){
        return $this->_getProductInStoreScope($this->getProduct()->getId())->isSalable();
    }

    /**
     * @return bool|int
     * @throws Exception
     */
    public function isInStock(){
        $product = $this->_getProductInStoreScope($this->getProduct()->getId());
        $stockItem = $this->_stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );
        return $stockItem->getIsInStock();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function getVisibility(){
        return $this->_getProductInStoreScope($this->getProduct()->getId())->getVisibility();
    }

    /**
     * @return null
     */
    public function getNeededIndexes(){
        if ($this->_neededIndexes == null) {
            $this->_neededIndexes = $this->_indexStatus->getNeededIndexes($this->getProduct()->getId());
        }
        return $this->_neededIndexes;
    }

    /**
     * @return bool
     */
    public function isIndexed(){
        $indexes = $this->getNeededIndexes();
        if (empty($indexes)){
            return true;
        }
        return false;
    }

    public function isFPCValid(){
        return false;
    }

    public function getFPCDetails(){
        return 'Cache Information';
    }





}
