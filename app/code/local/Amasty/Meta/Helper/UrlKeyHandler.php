<?php

class Amasty_Meta_Helper_UrlKeyHandler extends Mage_Core_Helper_Abstract
{
	protected $_connection;
	protected $_tablePrefix;
	protected $_productTypeId;
	protected $_urlPathId;
	protected $_urlKeyId;


	public function __construct()
	{
		//connection
		$this->_connection = Mage::getSingleton('core/resource')->getConnection('core_write');

		//table prefix
		$this->_tablePrefix = (string) Mage::getConfig()->getTablePrefix();

		//product type id
		$select               = $this->_connection->select()->from($this->_tablePrefix . 'eav_entity_type')
			->where("entity_type_code = 'catalog_product'");
		$this->_productTypeId = $this->_connection->fetchOne($select);

		//url path id
		$select           = $this->_connection->select()->from($this->_tablePrefix . 'eav_attribute')
			->where("entity_type_id = $this->_productTypeId AND (attribute_code = 'url_path')");
		$this->_urlPathId = $this->_connection->fetchOne($select);

		//url key id
		$select          = $this->_connection->select()->from($this->_tablePrefix . 'eav_attribute')
			->where("entity_type_id = $this->_productTypeId AND (attribute_code = 'url_key')");
		$this->_urlKeyId = $this->_connection->fetchOne($select);
	}

	/**
	 * @param $urlKeyTemplate
	 * @param array $stores
	 */
	public function process($urlKeyTemplate, $stores = array())
	{
		$stepLength = 100;

		$storeEntities = Mage::app()->getStores(true, true);
		if (! empty($stores)) {
			foreach ($storeEntities as $key => $storeEntity) {
				if (! in_array($key, $stores)) {
					unset($storeEntities[$key]);
				}
			}
		}

		foreach ($storeEntities as $store) {
			$step = 1;
			while (true) {
				$products = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect('*')
					->setCurPage($step)
					->setPageSize($stepLength)
					->setStore($store);

				foreach ($products as $product) {
					$this->processProduct($product, $store, $urlKeyTemplate);

					$product->setData('url_key_create_redirect', true);
					Mage::getSingleton('index/indexer')->processEntityAction(
						$product, Mage_Catalog_Model_Product::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
					);
				}

				if ($step ++ == $products->getLastPageNumber()) {
					break;
				}
			}
		}
	}

	public function processProduct(Mage_Catalog_Model_Product $product, $store, $urlKeyTemplate = '')
	{
		if (empty($urlKeyTemplate)) {
			$urlKeyTemplate = trim(Mage::getStoreConfig('ammeta/product/url_template', $store));
		}

		if (empty($urlKeyTemplate)) {
			return;
		}

		/** @var Amasty_Meta_Helper_Data $helper */
		$helper = Mage::helper('ammeta');

		$product->setStoreId($store->getId());
		$urlKey = $helper->cleanEntityToCollection()
			->addEntityToCollection($product)
			->parse($urlKeyTemplate, true);

		$urlKey = $product->formatUrlKey($urlKey);

		//update url_key
		$this->_updateUrlKey($product, $store->getId(), $urlKey);
		//update url_path
		$this->_updateUrlPath($product, $store->getId(), $urlKey,
			Mage::getStoreConfig('catalog/seo/product_url_suffix', $store));

		$product->setUrlKey($urlKey);
	}

	/**
	 * @param $product
	 * @param $storeId
	 * @param $urlKey
	 * @param string $urlSuffix
	 */
	protected function _updateUrlKey($product, $storeId, $urlKey, $urlSuffix = '')
	{
		$this->_updateAttribute($this->_urlKeyId, $product, $storeId, $urlKey, $urlSuffix);
	}

	/**
	 * @param $product
	 * @param $storeId
	 * @param $urlKey
	 * @param string $urlSuffix
	 */
	protected function _updateUrlPath($product, $storeId, $urlKey, $urlSuffix = '')
	{
		$this->_updateAttribute($this->_urlPathId, $product, $storeId, $urlKey, $urlSuffix);
	}

	/**
	 * @param $attributeId
	 * @param $product
	 * @param $storeId
	 * @param $urlKey
	 * @param $urlSuffix
	 */
	protected function _updateAttribute($attributeId, $product, $storeId, $urlKey, $urlSuffix)
	{
		$select = $this->_connection->select()->from($this->_tablePrefix . 'catalog_product_entity_varchar')
			->where("entity_type_id = $this->_productTypeId AND attribute_id = $this->_urlKeyId AND entity_id = {$product->getId()} AND store_id = {$storeId}");
		$row    = $this->_connection->fetchRow($select);

		if ($row) {
			$this->_connection->update($this->_tablePrefix . 'catalog_product_entity_varchar',
				array('value' => $urlKey . $urlSuffix),
				"entity_type_id = $this->_productTypeId AND attribute_id = $attributeId AND entity_id = {$product->getId()} AND store_id = {$storeId}");
		} else {
			$data = array(
				'entity_type_id' => $this->_productTypeId,
				'attribute_id'   => $attributeId,
				'entity_id'      => $product->getId(),
				'store_id'       => $storeId,
				'value'          => $urlKey . $urlSuffix
			);
			$this->_connection->insert($this->_tablePrefix . 'catalog_product_entity_varchar', $data);
		}
	}

}