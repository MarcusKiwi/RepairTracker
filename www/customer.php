<?php

function cContact($phone,$facebook) {
	if($phone!='') {
		return '<a href="tel:'.$phone.'">'.$phone.'</a>';
	} elseif ($facebook!='') {
		return '<a href="https://www.facebook.com/'.$facebook.'/">'.$facebook.'</a>';
	} else {
		return '<span class="none">none</span>';
	}
}

function cNoteStar($notes) {
	if($notes!='') {
		return '*';
	} else {
		return '';
	}
}

function cList($query) {
	global $DB;
	$stmt = $DB->query('SELECT cid,name,phone,facebook,area,notes FROM custs '.$query.';');
	$stmt->execute();
	$custs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if($stmt->rowCount()!=0) {
		$o = '';
		foreach($custs as $c) {
			$o .= '
			<tr>
				<td><a href="?i=C'.$c['cid'].'">C'.$c['cid'].'</a></td>
				<td><a href="?i=C'.$c['cid'].'">'.$c['name'].cNoteStar($c['notes']).'</a></td>
				<td>'.cContact($c['phone'],$c['facebook']).'</td>
				<td>'.$c['area'].'</td>
				<td><a href="?n=J&amp;c=C'.$c['cid'].'">New Job</a></td>
			</tr>';
		}
	} else {
		$o = '
		<tr><td>
		<span class="none">none</span>
		</td></tr>';
	}
	return '<div><table>'.$o.'</table></div>';
}

function cDetail($cid='',$name='',$phone='',$facebook='',$address='',$area='',$notes='') {
	// prep customer info
	if($cid!='') {
		$PageHeading = 'C'.$cid.' - '.$name;
		$FormAction = '?i=C'.$cid;
		$NewJobButton = '<button type="button" onclick="location.href=\'?n=J&amp;c=C'.$cid.'\'">New Job</button>';
		$HiddenCID = '<input type="hidden" name="cid" value="'.$cid.'">';
	} else {
		$PageHeading = 'New Customer';
		$FormAction = '?n=C';
		$NewJobButton = '';
		$HiddenCID = '';
	}
	if($phone!='') {
		$PhoneLink = '<a href="tel:'.$phone.'">📞</a>';
	} else {
		$PhoneLink = '';
	}
	if($facebook!='') {
		$FacebookLink = '<a href="https://www.facebook.com/'.$facebook.'/">📟</a>';
	} else {
		$FacebookLink = '';
	}
	// output form html
	return '
	<h1>'.$PageHeading.'</h1>
	<form method="post" action="'.$FormAction.'">
		'.NavBar().'
		<p>
			<button type="reset">Revert</button>
			<button type="submit">Save</button>
			'.$NewJobButton.'
		</p>
		'.$HiddenCID.'
		<table>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" value="'.$name.'" maxlength="128"></td>
			</tr>
			<tr>
				<td>Phone:</td>
				<td>
					<input type="text" name="phone" value="'.$phone.'" maxlength="32"> '.$PhoneLink.'
				</td>
			</tr>
			<tr>
				<td>Facebook:</td>
				<td>
					<input type="text" name="facebook" value="'.$facebook.'" maxlength="512"> '.$FacebookLink.'
				</td>
			</tr>
			<tr>
				<td>Address:</td>
				<td><input type="text" name="address" value="'.$address.'" maxlength="512"></td>
			</tr>
			<tr>
				<td>Area:</td>
				<td><input type="text" name="area" value="'.$area.'" maxlength="32"></td>
			</tr>
		</table>
		<h2>Notes</h2>
		<p>
			<textarea name="notes">'.$notes.'</textarea>
		</p>
	</form>';
}

function cInfo($cid) {
	global $DB;
	// action updated customer info
	if(ISSET($_POST['cid'])) {
		try {
			$stmt = $DB->prepare('UPDATE custs SET name=:name, phone=:phone, facebook=:facebook, address=:address, area=:area, notes=:notes 
				WHERE cid=:cid LIMIT 1;');
			$stmt->bindValue(':name',$_POST['name'],PDO::PARAM_STR);
			$stmt->bindValue(':phone',$_POST['phone'],PDO::PARAM_STR);
			$stmt->bindValue(':facebook',$_POST['facebook'],PDO::PARAM_STR);
			$stmt->bindValue(':address',$_POST['address'],PDO::PARAM_STR);
			$stmt->bindValue(':area',$_POST['area'],PDO::PARAM_STR);
			$stmt->bindValue(':notes',$_POST['notes'],PDO::PARAM_STR);
			$stmt->bindValue(':cid',$cid,PDO::PARAM_INT);
			$stmt->execute();
		} catch(Exception $ex) {
			$page = '
			<h1>Error</h1>
			<p>
			Database Error: <code>'.$ex->getMessage().'</code>
			</p>';
			return array('Error', "error", $page);
		}
		redirect('?i=C'.$cid);
		exit;
	// show customer info page
	} else {
		global $STATUS,$TASK;
		// customer info
		$stmt = $DB->prepare('SELECT cid,name,phone,facebook,address,area,notes FROM custs WHERE cid=:cid LIMIT 1;');
		$stmt->bindValue(':cid',$cid,PDO::PARAM_INT);
		$stmt->execute();
		$c = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
		$page = cDetail($c['cid'],$c['name'],$c['phone'],$c['facebook'],$c['address'],$c['area'],$c['notes']);
		// job history
		$stmt = $DB->prepare('SELECT jid,price,status,task,model FROM jobs WHERE cid=:cid ORDER BY jid DESC;');
		$stmt->bindValue(':cid',$cid,PDO::PARAM_INT);
		$stmt->execute();
		$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$page .= '<h2>History</h2><table>';
		foreach($jobs as $j) {
			$page .= '
			<tr>
				<td><a href="?i=J'.$j['jid'].'">J'.$j['jid'].' $'.$j['price'].' '.$STATUS[$j['status']].'</a></td>
				<td>'.$TASK[$j['task']].'</td>
				<td>'.$j['model'].'</td>
			</tr>';
		}
		$page .= '</table>';
		return array('C'.$c['cid'].' '.$c['name'], "cust", $page);
	}
}

function cNew() {
	// action new customer info
	if(ISSET($_POST['name'])) {
		global $DB;
		try {
			$stmt = $DB->query('SELECT cid FROM custs ORDER BY cid DESC LIMIT 1;');
			$stmt->execute();
			$c = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
			$stmt = $DB->prepare('INSERT INTO custs (cid, name, phone, facebook, address, area, notes) VALUES (:cid, :name, :phone, :facebook, :address, :area, :notes);');
			$stmt->bindValue(':cid',($c['cid']+1),PDO::PARAM_INT);
			$stmt->bindValue(':name',$_POST['name'],PDO::PARAM_STR);
			$stmt->bindValue(':phone',$_POST['phone'],PDO::PARAM_STR);
			$stmt->bindValue(':facebook',$_POST['facebook'],PDO::PARAM_STR);
			$stmt->bindValue(':address',$_POST['address'],PDO::PARAM_STR);
			$stmt->bindValue(':area',$_POST['area'],PDO::PARAM_STR);
			$stmt->bindValue(':notes',$_POST['notes'],PDO::PARAM_STR);
			$stmt->execute();
		} catch(Exception $ex) {
			$page = '
			<h1>Error</h1>
			<p>
			Database Error: <code>'.$ex->getMessage().'</code>
			</p>';
			return array('Error', "error", $page);
		}
		redirect('?i=C'.($c['cid']+1));
		exit;
	// show new customer page
	} else {
		return array("New Customer", "cust", cDetail());
	}
}

?>