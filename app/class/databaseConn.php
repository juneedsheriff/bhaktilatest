<?php date_default_timezone_set("Asia/Kolkata");
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

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$this->dbLink = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		if (!$this->dbLink) {
			return;
		}

		$this->dbLink->query("SET character_set_results=utf8");
		mb_language('uni');
		mb_internal_encoding('UTF-8');
		$this->dbLink->query("set names 'utf8'");
	}

	function isConnected()
	{
		return $this->dbLink instanceof mysqli;
	}

	function safeQuery($sql)
	{
		if (!$this->isConnected()) {
			return false;
		}

		try {
			return @$this->dbLink->query($sql);
		} catch (Throwable $e) {
			return false;
		}
	}

	function convertToLocalHtml($localHtmlEquivalent)
	{
		$localHtmlEquivalent = mb_convert_encoding($localHtmlEquivalent, "HTML-ENTITIES", "UTF-8");
		return $localHtmlEquivalent;
	}

	function getSelectQueryResult($selectQuery)
	{
		if (!$this->isConnected()) {
			return false;
		}

		$this->dbLink->query("SET character_set_results=utf8");
		$this->sqlQuery = $selectQuery;
		$this->dbResult = $this->safeQuery($this->sqlQuery);
		return $this->dbResult;
	}

	function updateData($updateQuery)
	{
		if (!$this->isConnected()) {
			return false;
		}

		$this->dbLink->query("SET character_set_results=utf8");
		$this->sqlQuery = $updateQuery;
		$this->dbResult = $this->safeQuery($this->sqlQuery);

		return (bool) $this->dbResult;
	}
}
