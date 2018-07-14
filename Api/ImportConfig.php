<?php

namespace BigBridge\ProductImport\Api;

/**
 * @author Patrick van Bergen
 */
class ImportConfig
{
    const DEFAULT_CATEGORY_PATH_SEPARATOR = '/';
    const TEMP_PRODUCT_IMAGE_PATH = BP . "/pub/media/import";

    /**
     * When set to true, no products are saved to the database
     *
     * @var bool
     */
    public $dryRun = false;

    /**
     * The number of products sent to the database at once
     * The number is a tested optimal balance between speed and database load.
     * Making the number larger will speed up import only marginally, and will create large transactions.
     *
     * @var int
     */
    public $batchSize = 1000;

    /**
     * @var callable
     *
     * This function will be called with the result of the import.
     *
     * Function signature:
     *
     * function(BigBridge\ProductImport\Model\Data\Product $product, $ok, $error);
     */
    public $resultCallback = null;

    /**
     * Create categories if they do not exist.
     *
     * true: creates categories
     * false: does not create categories, adds an error to the product
     *
     * @var bool
     */
    public $autoCreateCategories = true;

    /**
     * An array of attribute codes of select or multiple select attributes whose options should be created by the import if they did not exist.
     *
     * @var array
     */
    public $autoCreateOptionAttributes = [];

    /**
     * How to handle varchar and text fields with value ""?
     *
     * @var string
     */
    public $emptyTextValueStrategy = self::EMPTY_TEXTUAL_VALUE_STRATEGY_IGNORE;

    const EMPTY_TEXTUAL_VALUE_STRATEGY_IGNORE = "ignore"; // skip it in the import
    const EMPTY_TEXTUAL_VALUE_STRATEGY_REMOVE = "remove"; // remove the attribute value from the product

    /**
     * How to handle datetime, decimal and integer fields with value ""?
     *
     * @var string
     */
    public $emptyNonTextValueStrategy = self::EMPTY_NONTEXTUAL_VALUE_STRATEGY_IGNORE;

    const EMPTY_NONTEXTUAL_VALUE_STRATEGY_IGNORE = "ignore"; // skip it in the import
    const EMPTY_NONTEXTUAL_VALUE_STRATEGY_REMOVE = "remove"; // remove the attribute value from the product

    /**
     * Create url keys based on name or sku?
     *
     * @var string
     */
    public $urlKeyScheme = self::URL_KEY_SCHEME_FROM_NAME;

    const URL_KEY_SCHEME_FROM_NAME = 'from-name';
    const URL_KEY_SCHEME_FROM_SKU = 'from-sku';

    /**
     * If a url key is generated, what should happen if that url key is already used by another product?
     *
     * - create an error
     * - add the sku to the url_key: 'white-dwarf-with-mask' becomes 'white-dwarf-with-mask-white-dwarf-11'
     * - add increasing serial number: 'white-dwarf-with-mask' becomes 'white-dwarf-with-mask-1'
     *
     * @var string
     */
    public $duplicateUrlKeyStrategy = self::DUPLICATE_KEY_STRATEGY_ERROR;

    const DUPLICATE_KEY_STRATEGY_ERROR = 'error';
    const DUPLICATE_KEY_STRATEGY_ADD_SKU = 'add-sku';
    const DUPLICATE_KEY_STRATEGY_ADD_SERIAL = 'add-serial';
    const DUPLICATE_KEY_STRATEGY_ALLOW = 'allow';

    /**
     * Categories are imported by paths of category-names, like this "Doors/Wooden Doors/Specials"
     * When your import set contains categories with a / in the name, like "Summer / Winter collection",
     * you may want to change the category name separator into something else, like "$"
     * Make sure to update the imported category paths when you do.
     *
     * @var string
     */
    public $categoryNamePathSeparator = self::DEFAULT_CATEGORY_PATH_SEPARATOR;

    /**
     * Base directory the source images with relative paths
     * By default: relative to the location of the
     * @var string|null
     */
    public $imageSourceDir = null;

    /**
     * Base directory where images will be cached during import.
     * @var string
     */
    public $imageCacheDir = self::TEMP_PRODUCT_IMAGE_PATH;

    /**
     * Downloading images can be slow. Choose your image strategy:
     * - force download: (default), images are downloaded over and over again
     * - check import dir: checks the directory where images are cached, pub/media/import first
     *
     * @var string
     */
    public $existingImageStrategy = self::EXISTING_IMAGE_STRATEGY_FORCE_DOWNLOAD;

    const EXISTING_IMAGE_STRATEGY_FORCE_DOWNLOAD = 'force-download';
    const EXISTING_IMAGE_STRATEGY_CHECK_IMPORT_DIR = 'check-import-dir';
    const EXISTING_IMAGE_STRATEGY_HTTP_CACHING = 'http-caching';

    /**
     * How to handle products that change type?
     *
     * @var string
     */
    public $productTypeChange = self::PRODUCT_TYPE_CHANGE_NON_DESTRUCTIVE;

    const PRODUCT_TYPE_CHANGE_ALLOWED = 'allowed'; // allow all product type changes
    const PRODUCT_TYPE_CHANGE_FORBIDDEN = 'forbidden'; // allow no product type changes
    const PRODUCT_TYPE_CHANGE_NON_DESTRUCTIVE = 'non-destructive'; // allow only product type changes that do not delete data
}