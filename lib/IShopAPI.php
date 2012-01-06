<?php

interface IShopAPI {
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
