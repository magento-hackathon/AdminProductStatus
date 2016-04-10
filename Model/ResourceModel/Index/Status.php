<?php
/**
 * Created by PhpStorm.
 * User: brobie
 * Date: 4/9/16
 * Time: 8:39 PM
 */

namespace MagentoHackathon\AdminProductStatus\Model\ResourceModel\Index;

class Status {

    protected $_resource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Resource $resource,
        array $data = []
    ) {
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_read');
        }
        return $this->connection;
    }

    public function getDirectQuery()
    {
        $table=$this->_resource->getTableName('catalog_product_entity');
        $sku = $this->getConnection()->fetchRow('SELECT sku,entity_id FROM ' . $table);
        return $sku;
    }

    public function isIndexingNeeded($productId, $store){
       $rows = $this->getConnection()->fetchAll("

select 	'catalog_product_price' indexname,
		(select max(version_id) from catalog_product_price_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalog_product_price') current_version
union all
select 	'cataloginventory_stock' indexname,
		(select max(version_id) from cataloginventory_stock_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'cataloginventory_stock') current_version
union all
select 	'catalog_category_product' indexname,
		(select max(version_id) from catalog_category_product_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalog_category_product') current_version
union all
select 	'catalog_product_category' indexname,
		(select max(version_id) from catalog_product_category_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalog_product_category') current_version
union all
select 	'catalog_product_attribute' indexname,
		(select max(version_id) from catalog_product_attribute_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalog_product_attribute') current_version
union all
select 	'catalogsearch_fulltext' indexname,
		(select max(version_id) from catalogsearch_fulltext_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalogsearch_fulltext') current_version
union all
select 	'catalogrule_product' indexname,
		(select max(version_id) from catalogrule_product_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalogrule_product') current_version
union all
select 	'catalogrule_rule' indexname,
		(select max(version_id) from catalogrule_rule_cl where entity_id = $productId) max_version,
		(select version_id from mview_state where view_id = 'catalogrule_rule') current_version


        ");




    }


}