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
    protected $connection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
       $this->_resource = $resource;
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_read');
        }
        return $this->connection;
    }


    public function getNeededIndexes($productId){
       $rows = $this->getConnection()->fetchAll("

        select 	'catalog_product_price' indexname,
                (case when ((select max(version_id) from catalog_product_price_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalog_product_price')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'cataloginventory_stock' indexname,
                (case when ((select max(version_id) from cataloginventory_stock_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'cataloginventory_stock')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'catalog_category_product' indexname,
                (case when ((select max(version_id) from catalog_category_product_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalog_category_product')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'catalog_product_category' indexname,
                (case when ((select max(version_id) from catalog_product_category_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalog_product_category')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'catalog_product_attribute' indexname,
                (case when ((select max(version_id) from catalog_product_attribute_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalog_product_attribute')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'catalogsearch_fulltext' indexname,
                (case when ((select max(version_id) from catalogsearch_fulltext_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalogsearch_fulltext')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'catalogrule_product' indexname,
                (case when ((select max(version_id) from catalogrule_product_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalogrule_product')) THEN 1 ELSE 0 END) as needs_index
        union all
        select 	'catalogrule_rule' indexname,
                (case when ((select max(version_id) from catalogrule_rule_cl where entity_id = $productId) >
                (select version_id from mview_state where view_id = 'catalogrule_rule') ) THEN 1 ELSE 0 END) as needs_index


        ");

        $needsIndex = array();

        foreach($rows as $row){
            if ($row['needs_index']){
                $needsIndex[] = $row['indexname'];
            }
        }

        return $needsIndex;

    }


}