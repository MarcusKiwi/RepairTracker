<?php

function NavBar() {
	return '
	<p class="navbar">
	<a href="?">🏠 Home</a>
	<a href="?l=JO">🚧 Open Jobs</a>
	<a href="?l=JA">⚰️ Old Jobs</a>
	<a href="?l=C">📇 All Customers</a>
	<a href="?n=C">😁 New Customer</a>
	<a href="?logout=please">🖐 Log Out</a>
	</p>';
}

function DashBoard() {
	global $STATUS,$TASK;
	$page = '
	<h1>Job Tracker</h1>
	'.NavBar().'
	<h2>Recent Customers</h2>
	'.cList('ORDER BY cid DESC LIMIT 10').'
	<h2>Open Jobs</h2>
	'.jList('WHERE status<200 ORDER BY status DESC,jid DESC').'
	<h2>Recent Jobs</h2>
	'.jList('ORDER BY jid DESC LIMIT 10');
	return array("", "dash", $page);
}

function lCust() {
	$page = '
	<h1>All Customers</h1>
	'.NavBar().'
	'.cList('ORDER BY cid DESC');
	return array('All Customers', "list", $page);
}

function lJobAll() {
	$page = '
	<h1>All Jobs</h1>
	'.NavBar().'
	'.jList('ORDER BY jid DESC');
	return array('All Customers', "list", $page);
}

function lJobOpen() {
	$page = '
	<h1>Open Jobs</h1>
	'.NavBar().'
	'.jList('WHERE status<200 ORDER BY status DESC,jid DESC');
	return array('All Customers', "list", $page);
}

?>