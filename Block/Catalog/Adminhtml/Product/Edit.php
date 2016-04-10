<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer edit block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
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
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->_stockRegistry = $stockRegistry;
        parent::__construct($context, $jsonEncoder, $attributeSetFactory, $registry, $productHelper, $data);
    }

    //@todo put this into the child block

    protected function _getProductInStoreScope($id, $store){
        if ($this->_productInStoreScope == null) {
            $this->_productInStoreScope = $this->_productRepository->getById($id, false, $store);
        }
        return $this->_productInStoreScope;
    }

    public function isVisibleOnFrontend(){
        return $this->_getProductInStoreScope($this->getProduct()->getId(), 1)->isSalable();
    }
    public function isInStock(){
        $product = $this->_getProductInStoreScope($this->getProduct()->getId(), 1);
        $stockItem = $this->_stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );
        return $stockItem->getIsInStock();
    }
    public function getVisibility(){
        return $this->_getProductInStoreScope($this->getProduct()->getId(), 1)->getVisibility();
    }

    public function isIndexed(){
        //Get a model
        //call ->needsIndex($product)
        return false;
    }

}
