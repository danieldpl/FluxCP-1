<?php
if (!defined('FLUX_ROOT')) exit;

$txnLogTable = Flux::config('FluxTables.TransactionTable');
$sqlpartial  = "{$server->loginDatabase}.{$txnLogTable} AS p LEFT OUTER JOIN {$server->loginDatabase}.login AS l";
$sqlpartial .= " ON p.account_id = l.account_id WHERE (p.server_name = ? OR p.server_name IS NULL OR p.server_name = '')";

$sth = $server->connection->getStatement("SELECT COUNT(id) AS total FROM $sqlpartial");
$sth->execute(array($session->loginAthenaGroup->serverName));

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(
	array(
		'process_date' => 'DESC',
		'txn_id',
		'payment_status',
		'payer_email',
		'mc_gross',
		'credits',
		'server_name',
		'userid'
	)
);

$sql = "SELECT p.*, l.userid FROM $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);

$sth->execute(array($session->loginAthenaGroup->serverName));
$transactions = $sth->fetchAll();
?>