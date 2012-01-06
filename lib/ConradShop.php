<?php

require_once("IShopAPI.php");
require_once("MasterClass.php");
require_once("ShopArticle.php");



class ConradShop extends MasterClass implements IShopAPI {
	/**
	 * Holds the url for getting Articles by id
	 * @var string
	 */
	private $articleByIdUrl = null;
	
	/**
	 * The Distributor name
	 */
	const DISTRIBUTOR_NAME = "Conrad";
	
	/**
	 * A pregexp, if an article ID was wrong.
	 * @var string
	 */
	private $articleNotFoundPregexp = '/leider konnten wir keinen artikel mit/i';
	
	
	/**
	 * An array of detail divs
	 * @var array
	 */
	private $detailDivIds = array (
			'/<div id="mc_info_\d{4,8}_produktbezeichnung">/i',
			'/<div id="mc_info_\d{4,8}_highlights">/i',
			'/<div id="mc_info_\d{4,8}_beschreibung">/i',
			'/<div id="mc_info_\d{4,8}_special">/i',
			'/<div id="mc_info_\d{4,8}_technischedaten">/i');
			
	
	
	/**
	 * Returns the Distributor Name.
	 */
	public function GetDistributorName() {
		return self::DISTRIBUTOR_NAME;
	}
	
	
	
	/**
	 * Searches an article by it's unique identifier in the shop in question.
	 * Returns a boolean false, if the article was not found, and an instance
	 * of an Article-Class, if found.
	 * 
	 * @param string $id
	 * @return (false|Article)
	 */
	public function GetArticleById($id) {
		$ch = curl_init();
		$callUrl = str_replace('{{id}}', $id, $this->articleByIdUrl);
		curl_setopt($ch, CURLOPT_URL, $callUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		if ($response === false)
			die("Could not get URL: ".curl_error($ch));
		
		if (preg_match($this->articleNotFoundPregexp, $response)) {
			return false;
		}
		
		$article = new ShopArticle();
		$article->Distributor = $this->GetDistributorName();
		$article->ArticleId   = $id;
		$article->ArticleUrl  = $callUrl;
		
		$this->extractPriceFromPagedata($response, $article);
		$this->extractDescriptionFromPagedata($response, $article);
		
		return $article;
	}
	
	
	/**
	 * Extracts price and currency and sets this to a reference of a
	 * ShopArticle-Instance.
	 *
	 * @param string      $pagedata
	 * @param ShopArticle &$article
	 * @return bool       $success
	 */
	private function extractPriceFromPagedata($pagedata, 
	                                          ShopArticle &$article ) {
		preg_match('/<span id="mc_info_\d{4,8}_produktpreis">.{0,5}€ ?(\d{1,5},\d\d)/i', 
				$pagedata, $matches);
		
		if (empty($matches[1]))
			return false;
		
		$article->Currency = 'EUR';
		$article->Price    = (float)str_replace(',', '.', $matches[1]);
		
		return true;
	}
	
	
	/**
	 * Extracts the description html part from the homepage.
	 *
	 * @param  string      $pagedata
	 * @param  ShopArticle &$article
	 * @return bool
	 */
	private function extractDescriptionFromPagedata($pagedata,
	                                                ShopArticle &$article) {
		$data = $this->extractDivFromHtml(
				'/<div class="inner" id="details">/i', $pagedata);
		if (!$data)
			return false;
		
		$description = '';
		foreach ($this->detailDivIds as $pregexp) {
			$part = $this->extractDivFromHtml($pregexp, $data);
			if ($part)
				$description .= $part;
		}
		
		$article->Description = $description;
		return true;
	}
	
	
	/**
	 * Grabs a complete div-structure from some starting point
	 * 
	 * @param  string $startPregexp
	 * @param  string $html
	 * @throws Exception
	 * @return (false|string)
	 */
	private function extractDivFromHtml($startPregexp, $html) {
		if (preg_match($startPregexp, $html, $matches) == 0) {
			return false;
		}
		$divLvl = 1;
		
		$openPos  = stripos($html, $matches[0]);
		$curPos   = $openPos+strlen($matches[0]);
		$closePos = null;
		
		do {
			unset($matches);
			preg_match('/<\/div>/i', $html, $matches, 0, $curPos);
			$closePos = stripos($html, $matches[0], $curPos);
			
			unset($matches);
			if (preg_match('/<div/i', $html, $matches, 0, $curPos))
				$nextOpenPos = strpos($html, $matches[0], $curPos);
			else
				$nextOpenPos = strlen($html);
			
			if ($closePos < $nextOpenPos) {
				$divLvl--;
				$curPos = $closePos+4;
			} else {
				$divLvl++;
				$curPos = $nextOpenPos+4;
			}
		} while ($divLvl > 0);
		
		if ($divLvl > 0)
			throw new Exception('Did not find a matching close-div-tag for the'.
			                    ' starting div.');
		
		return substr($html, $openPos, $closePos - $openPos + 6);
	}
	
	
	
	/**
	 * Setter for ArticleByIdUrl, does some validation
	 *
	 * @param  string $value
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	public function SetArticleByIdUrl($value) {
		if (!is_string($value))
			throw new InvalidArgumentException(
					'ArticleByIdUrl needs to be a string.');
		
		if (!preg_match('/.*\{\{id\}\}.*/', $value))
			throw new InvalidArgumentException(
					'ArticleByIdUrl needs a "{{id}}"-tag - I don\'t know '.
					'where to set the id-value');
		
		$this->articleByIdUrl = $value;
		return true;
	}
}
