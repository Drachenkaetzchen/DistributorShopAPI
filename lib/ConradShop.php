<?php

require_once("IShopAPI.php");
require_once("MasterClass.php");



class ConradShop extends MasterClass implements IShopAPI {
	/**
	 * Holds the url for getting Articles by id
	 * @var string
	 */
	private $articleByIdUrl = null;
	
	
	
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
		
		if (preg_match('/Leider konnten wir keinen Artikel mit/', $response)) {
			return false;
		}
		
		return new StdClass();
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
