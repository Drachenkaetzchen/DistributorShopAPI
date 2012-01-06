<?php

interface IShopAPI {
	/**
	 * Return the Distributor name
	 * @return string
	 */
	public function GetDistributorName();

	/**
	 * Searches an article by it's unique identifier in the shop in question.
	 * Returns a boolean false, if the article was not found, and an instance
	 * of an Article-Class, if found.
	 * 
	 * @param string $id
	 * @return (false|ShopArticle)
	 */
	public function GetArticleById($id);
}
