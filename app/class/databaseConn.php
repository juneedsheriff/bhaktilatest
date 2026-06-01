<?php date_default_timezone_set("Asia/Kolkata");
session_start();
define('DB_HOST', '103.21.58.6:3306');

define('DB_USER', 'bhaktiapp');

define('DB_PASSWORD', 'IHn1kCe1e@qo9f$h');

define('DB_DATABASE', 'kalyafbj_bhakti-latest');
class DatabaseConn
{
	var $dbLink;
	var $sqlQuery;
	var $dbResult;
	var $dbRow;

	function __construct()
	{
		$this->dbLink = '';
		$this->sqlQuery = '';
		$this->dbResult = '';
		$this->dbRow = '';

		$this->dbLink = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		$this->dbLink->query("SET character_set_results=utf8");
		mb_language('uni');
		mb_internal_encoding('UTF-8');
		$this->dbLink->query("set names 'utf8'");
	}

	function convertToLocalHtml($localHtmlEquivalent)
	{
		$localHtmlEquivalent = mb_convert_encoding($localHtmlEquivalent, "HTML-ENTITIES", "UTF-8");
		return $localHtmlEquivalent;
	}

	function getSelectQueryResult($selectQuery)
	{
		$this->dbLink->query("SET character_set_results=utf8");
		$this->sqlQuery = $selectQuery;
		$this->dbResult = $this->dbLink->query($this->sqlQuery);
		return $this->dbResult;
	}

	function updateData($updateQuery)
	{
		$this->dbLink->query("SET character_set_results=utf8");
		$this->sqlQuery = $updateQuery;
		$this->dbResult = $this->dbLink->query($this->sqlQuery);

		if ($this->dbResult)
			return true;
		else
			return false;
	}
}
