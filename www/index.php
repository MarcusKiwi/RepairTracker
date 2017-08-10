<?php

// INIT
include("list.php");
include("customer.php");
include("job.php");
$SESSIONTIME = (60*30); // 30 mins
$PASSWORD = 'password';
$DEFAULTPRICE = 50;
$DB = new PDO('mysql:host=localhost;dbname=repairs;charset=utf8mb4','username','password');
$STATUS = array(
	  0 => 'Lead',
	 10 => 'Cancelled',
	 50 => 'On-Hold',
	100 => 'In-Progress',
	150 => 'Finished-Unpaid',
	200 => 'Complete'
);
$TYPE = array(
	 0 => ' ',
	10 => 'Laptop',
	11 => 'Netbook',
	12 => 'Hybrid',
	20 => 'Desktop',
	21 => 'All-In-One'
);
$OS = array(
	  0 => ' ',
	  5 => 'WinXP',
	  6 => 'WinVista',
	  7 => 'Win7',
	  8 => 'Win8',
	 10 => 'Win10',
	100 => 'Linux',
	200 => 'Mac',
	255 => 'Other'
);
$TASK = array(
	  0 => ' ',
	  1 => 'Reinstall OS',
	  2 => 'Dead Disk',
	  3 => 'Misc Hardware',
	  4 => 'Data Recovery',
	255 => 'Other'
);

// REDIRECT
function redirect($page='') {
	if(ISSET($_SERVER['HTTPS'])) {
		$proto = 'https';
	} else {
		$proto = 'http';
	}
	header('Location: '.$proto.'://'.$_SERVER['HTTP_HOST'].str_replace('index.php','',$_SERVER['SCRIPT_NAME']).$page);
	exit;
}

// LOGIN PAGE
function LoginPage() {
	$page = '
	<h1>Password Required</h1>
	<form method="post" action="?">
		<p>
			<input type="password" name="pass">
			<button type="submit">Log In</button>
		</p>
	</form>
	';
	return array("", "login", $page);
}

// LOGINS
session_start();
// existing logins
$ValidUser = FALSE;
if(isset($_SESSION['expires'])) {
	$expiry = $_SESSION['expires'] - time();
	if($expiry > 0) {
		$_SESSION['expires'] = time() + $SESSIONTIME;
		$ValidUser = TRUE;
	} else {
		session_unset();
		session_destroy();
	}
}
// new logins
if(isset($_POST['pass'])) {
	if($_POST['pass']==$PASSWORD) {
		$_SESSION['expires'] = time() + $SESSIONTIME;
		redirect('?');
		exit;
	}
}
// logout
if(isset($_GET['logout'])) {
	session_unset();
	session_destroy();
	redirect('?');
	exit;
}

// PAGE ROUTING
if($ValidUser==TRUE) {
	if((ISSET($_GET['i'])) || (ISSET($_GET['n'])) || (ISSET($_GET['l']))) {
		// info pages
		if(ISSET($_GET['i'])) {
			$idNum = substr($_GET['i'],1);
			switch(strtolower($_GET['i'][0])) {
				case 'c':
					// ?i=C1000 - Customer Info
					$pg = cInfo($idNum); break;
				case 'j':
					// ?i=J1000 - Job Info
					$pg = jInfo($idNum); break;
				default:
					$pg = DashBoard(); break;
			}
		// new pages
		} elseif(ISSET($_GET['n'])) {
			switch(strtolower($_GET['n'][0])) {
				case 'c':
					// ?n=C - New Customer
					$pg = cNew(); break;
				case 'j':
					// ?n=J&c=C1000 - New Job for Customer
					$cidNum = substr($_GET['c'],1);
					$pg = jNew($cidNum); break;
				default:
					$pg = DashBoard(); break;
			}
		// list pages
		} elseif(ISSET($_GET['l'])) {
			switch(strtolower($_GET['l'])) {
				case 'c':
					// ?l=C - List Customers
					$pg = lCust(); break;
				case 'jo':
					// ?l=JO - List Open Jobs
					$pg = lJobOpen(); break;
				case 'ja':
					// ?l=JA - List All Jobs
					$pg = lJobAll(); break;
				default:
					$pg = DashBoard(); break;
			}
		}
	} else {
		// Dashboard Page
		$pg = DashBoard();
	}
} else {
	$pg = LoginPage();
}

// PAGE DISPLAY
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php if($pg[0]!='') { echo $pg[0].' - '; } ?>Job Tracker</title>
<meta id="meta" name="viewport" content="width=device-width; initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
</head>
<?php
if($pg[1]!='') {
	echo '<body class="'.$pg[1].'">';
} else {
	echo '<body>';
}
echo "\n\n".$pg[2]."\n\n";
?>
</body>
</html>
