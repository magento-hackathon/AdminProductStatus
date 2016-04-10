<?php
/**
 * Created by PhpStorm.
 * User: brobie
 * Date: 4/9/16
 * Time: 8:39 PM
 */

namespace MagentoHackathon\AdminProductStatus\Model\ResourceModel\Index;

class Status implements StatusInterface {

    protected $_resource;
    protected $_indexerFactory;
    protected $connection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
       $this->_resource = $resource;
        $this->_indexerFactory = $indexerFactory;
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_read');
        }
        return $this->connection;
    }


    public function getNeededIndexes($productId){

        $sql = "";

        $sql = $this->_getSelectPart($sql, 'catalog_product_price', $productId);
        $sql = $this->_getSelectPart($sql, 'cataloginventory_stock', $productId);
        $sql = $this->_getSelectPart($sql, 'catalog_category_product', $productId);
        $sql = $this->_getSelectPart($sql, 'catalog_product_category', $productId);
        $sql = $this->_getSelectPart($sql, 'catalog_product_attribute', $productId);
        $sql = $this->_getSelectPart($sql, 'catalogsearch_fulltext', $productId);
        $sql = $this->_getSelectPart($sql, 'catalogrule_product', $productId);
        $sql = $this->_getSelectPart($sql, 'catalogrule_rule', $productId);


        $needsIndex = array();

        if ($sql) {
            $rows = $this->getConnection()->fetchAll($sql);

            foreach ($rows as $row) {
                if ($row['needs_index']) {
                    $needsIndex[] = $row['indexname'];
                }
            }
        }
        return $needsIndex;

    }

    private function _getSelectPart($sql, $indexer, $productId){
        if ($this->_indexerFactory->create()->load($indexer)->isScheduled()) {
            if ($sql) {
                $sql .= " union all ";
            }
            $sql .= "select '$indexer' indexname,
                (case
                  when (
                    ((select max(version_id) from $indexer"."_cl where entity_id = $productId) > (select version_id from mview_state where view_id = '$indexer'))
                       or ((select max(version_id) from catalog_product_price_cl where entity_id = $productId) is not null and ((select version_id from mview_state where view_id = 'catalog_product_price') is null))
                  ) THEN 1 ELSE 0 END) as needs_index";
        }
        return $sql;
    }

}