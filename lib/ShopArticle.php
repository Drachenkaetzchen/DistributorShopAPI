<?php

require_once("MasterClass.php");

class ShopArticle extends MasterClass {
	/**
	 * The distributor of this article (required)
	 * @var string
	 */
	public $Distributor = null;
	
	/**
	 * The unique identifier on the distributor-site (required)
	 * @var mixed
	 */
	public $ArticleId = null;
	
	/**
	 * The url, from which the article has been extracted (required)
	 * @var string
	 */
	public $ArticleUrl = null;
	
	/**
	 * The price in the currency
	 * @var float
	 */
	public $Price = null;
	
	/**
	 * The currency's ISO 4217 code, in which the price is given.
	 * @var     string
	 * @see     http://en.wikipedia.org/wiki/ISO_4217
	 * @example "EUR", "USD", "GBP"
	 */
	public $Description = null;
	
	/**
	 * An array of attributes, the values being strings with value
	 * and (optionally) data type. The difference to the description is
	 * the data being available in a key->value-format on the distributor
	 * data source.
	 *
	 * @var     array
	 * @example
	 *    $attributes = array (
	 *        'Farbe' => 'Weiss',
	 *        'Gehäuse' => '5 mm',
	 *        'Ausführung' => 'klar');
	 */
	public $Attributes = null;
	
	/**
	 * The Urls of datasheets.
	 * @var array
	 * @example
	 *     $datasheetUrls = array (
	 *         'datasheet' => 'http://example.com/1',
	 *         'manual' => 'http://example.com/14',
	 *         'cert. of conformity' => 'http://example.com/foobar');
	 */
	public $DatasheetUrls = null;
	
	/**
	 * Availability-code for not available
	 */
	const AVAILABILITY_UNAVAILABLE = 1;
	
	/**
	 * Availability-code for "somewhen in the near future"
	 */
	const AVAILABILITY_NEAR_FUTURE = 2;
	
	/**
	 * Availability-code for "directly available"
	 */
	const AVAILABILITY_AVAILABLE   = 3;
	
	/**
	 * A representation of the availability.
	 * @see self::AVAILABILITY_* constants
	 * @var int
	 */
	public $Availability = null;
}
