<?php

namespace BigBridge\ProductImport\Api\Data;

use BigBridge\ProductImport\Model\Data\Image;
use BigBridge\ProductImport\Model\Data\LinkInfo;

/**
 * @author Patrick van Bergen
 */
abstract class Product
{
    const PLACEHOLDER_LINE_NUMBER = "-1";

    const GLOBAL_STORE_VIEW_CODE = 'admin';

    const DEFAULT_STOCK_NAME = 'Default';

    const PLACEHOLDER_NAME = 'Product Placeholder';
    const PLACEHOLDER_PRICE = '123456.78';

    const CATEGORY_IDS = 'category_ids';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const WEBSITE_IDS = 'website_ids';

    const PRICE_TYPE_FIXED = 'fixed';
    const PRICE_TYPE_PERCENT = 'percent';

    /** @var  int */
    public $id;

    /** @var  int */
    protected $attribute_set_id;

    /** @var  string 64 character */
    protected $sku;

    /** @var int[] */
    protected $category_ids = [];

    /** @var array  */
    protected $website_ids = [];

    /** @var ProductStoreView[] */
    protected $storeViews = [];

    /** @var ProductStockItem[] */
    protected $stockItems = [];

    /** @var Image[] */
    protected $images = [];

    /** @var string[][] */
    protected $linkedProductSkus = [];

    /** @var int[][] */
    protected $linkedProductIds = [];

    /** @var TierPrice[]|null An array of tier prices. null means: not used in this import */
    protected $tierPrices = null;

    /** @var CustomOption[]|null */
    protected $customOptions = null;

    /** @var array  */
    protected $unresolvedAttributes = [];

    // =========================================
    // importer data
    // =========================================

    /** @var  array */
    protected $errors = [];

    /** @var string  */
    public $lineNumber = "";

    public function __construct(string $sku)
    {
        $this->storeViews[self::GLOBAL_STORE_VIEW_CODE] = new ProductStoreView();
        $this->sku = trim($sku);
    }

    public function isOk(): bool
    {
        return empty($this->errors);
    }

    public function addError(string $error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Used in catalog_product_entity table
     * @return string
     */
    public abstract function getType();

    /**
     * Used in catalog_product_entity table
     * @return string
     */
    public function getHasOptions()
    {
        return '0';
    }

    /**
     * Used in catalog_product_entity table
     * @return string
     */
    public function getRequiredOptions()
    {
        return '0';
    }

    /**
     * @param string $storeViewCode
     * @return ProductStoreView
     */
    public function storeView(string $storeViewCode) {
        $storeViewCode = trim($storeViewCode);
        if (!array_key_exists($storeViewCode, $this->storeViews)) {
            $this->storeViews[$storeViewCode] = new ProductStoreView();
        }
        return $this->storeViews[$storeViewCode];
    }

    /**
     * @return ProductStoreView
     */
    public function global() {
        return $this->storeViews[self::GLOBAL_STORE_VIEW_CODE];
    }

    /**
     * @return ProductStoreView[]
     */
    public function getStoreViews()
    {
        return $this->storeViews;
    }

    /**
     * @return ProductStockItem
     */
    public function defaultStockItem()
    {
        if (!array_key_exists(self::DEFAULT_STOCK_NAME, $this->stockItems)) {
            $this->stockItems[self::DEFAULT_STOCK_NAME] = new ProductStockItem();
        }
        return $this->stockItems[self::DEFAULT_STOCK_NAME];
    }

    /**
     * @return ProductStockItem[]
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    public function setCategoryIds(array $categoryIds)
    {
        $this->category_ids = $categoryIds;
    }

    /**
     * @return int[]
     */
    public function getCategoryIds()
    {
        return $this->category_ids;
    }

    /**
     * @param array $categoryNames An array of category name paths (i.e. ['Books/Novels', 'Books/Sci-Fi/Foreign'].
     */
    public function setCategoriesByGlobalName(array $categoryNames)
    {
        $this->unresolvedAttributes[self::CATEGORY_IDS] = $categoryNames;
    }

    public function setAttributeSetId(int $attributeSetId)
    {
        $this->attribute_set_id = $attributeSetId;
    }

    /**
     * @return int|null
     */
    public function getAttributeSetId()
    {
        return $this->attribute_set_id;
    }

    public function removeAttributeSetId()
    {
        $this->attribute_set_id = null;
    }

    /**
     * @param string $attributeSetName An attribute set name
     */
    public function setAttributeSetByName(string $attributeSetName)
    {
        $this->unresolvedAttributes[self::ATTRIBUTE_SET_ID] = $attributeSetName;
    }

    public function setWebsitesByCode(array $websiteCodes)
    {
        $this->unresolvedAttributes[self::WEBSITE_IDS] = $websiteCodes;
    }

    /**
     * @param int[] $websiteIds
     */
    public function setWebsitesIds(array $websiteIds)
    {
        $this->website_ids = $websiteIds;
    }

    /**
     * @return int[]
     */
    public function getWebsiteIds()
    {
        return $this->website_ids;
    }

    public function removeWebsiteIds()
    {
        $this->website_ids = [];
    }

    /**
     * @param string $imagePath Absolute path to JPEG or PNG image
     * @return Image
     */
    public function addImage(string $imagePath)
    {
        $image = new Image($imagePath);
        $this->images[] = $image;
        return $image;
    }

    /**
     * @return Image[]
     */
    public function getImages()
    {
        return $this->images;
    }

    public function setRelatedProductSkus(array $skus)
    {
        $this->linkedProductSkus[LinkInfo::RELATED] = array_map('trim', $skus);
    }

    public function setUpSellProductSkus(array $skus)
    {
        $this->linkedProductSkus[LinkInfo::UP_SELL] = array_map('trim', $skus);
    }

    public function setCrossSellProductSkus(array $skus)
    {
        $this->linkedProductSkus[LinkInfo::CROSS_SELL] = array_map('trim', $skus);
    }

    public function setRelatedProductId(array $ids)
    {
        $this->linkedProductIds[LinkInfo::RELATED] = array_map('trim', $ids);
    }

    public function setUpSellProductIds(array $ids)
    {
        $this->linkedProductIds[LinkInfo::UP_SELL] = array_map('trim', $ids);
    }

    public function setCrossSellProductIds(array $ids)
    {
        $this->linkedProductIds[LinkInfo::CROSS_SELL] = array_map('trim', $ids);
    }

    public function getLinkedProductSkus(): array
    {
        return $this->linkedProductSkus;
    }

    /**
     * Returns the ids of linked products of the given type
     * A return of null denotes that the user has not specified any links, and existing links should not be modified
     *
     * @param string $linkType
     * @return int[]|null
     */
    public function getLinkedProductIds(string $linkType)
    {
        return array_key_exists($linkType, $this->linkedProductIds) ? $this->linkedProductIds[$linkType] : null;
    }

    /**
     * @param TierPrice[] $tierPrices
     */
    public function setTierPrices(array $tierPrices)
    {
        $this->tierPrices = $tierPrices;
    }

    /**
     * @return TierPrice[]|null
     */
    public function getTierPrices()
    {
        return $this->tierPrices;
    }

    /**
     * @return array
     */
    public function getUnresolvedAttributes()
    {
        return $this->unresolvedAttributes;
    }

    /**
     * @param array $customOptions
     */
    public function setCustomOptions(array $customOptions)
    {
        $this->customOptions = $customOptions;
    }

    /**
     * @return CustomOption[]|null
     */
    public function getCustomOptions()
    {
        return $this->customOptions;
    }
}
