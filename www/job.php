<?php

function jList($query) {
	global $DB;
	global $TASK,$STATUS;
	$stmt = $DB->query('SELECT jid,cid,jobdate,price,status,task,model FROM jobs '.$query.';');
	$stmt->execute();
	$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if($stmt->rowCount()!=0) {
		$o = '';
		foreach($jobs as $j) {
			$stmt = $DB->prepare('SELECT name,notes FROM custs WHERE cid=:cid LIMIT 1;');
			$stmt->bindValue(':cid',$j['cid'],PDO::PARAM_INT);
			$stmt->execute();
			$c = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
			$o .= '
			<tr>
			<td><a href="?i=J'.$j['jid'].'">J'.$j['jid'].'</a></td>
			<td>'.$j['jobdate'].'</td>
			<td class="number">$'.$j['price'].'</td>
			<td>'.$STATUS[$j['status']].'</td>
			<td>'.$TASK[$j['task']].'</td>
			<td>'.$j['model'].'</td>
			<td><a href="?i=C'.$j['cid'].'">C'.$j['cid'].'</a></td>
			<td><a href="?i=C'.$j['cid'].'">'.$c['name'].cNoteStar($c['notes']).'</a></td>
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

function jDetailOptionList($haystack,$needle) {
	if($needle=='') {
		$needle = 0;
	}
	$o = '';
	foreach($haystack as $k => $v) {
		if($k==$needle) {
			$s = ' selected="selected"';
		} else {
			$s = '';
		}
		$o .= '<option value="'.$k.'"'.$s.'>'.str_replace(' ','&nbsp;',$v).'</option>';
	}
	return $o;
}

function jDetail($cid,$jid='',$jobdate='',$status='',$price='0',$task='',$notes='',$type='',$model='',$os='',$cpu='',$ram='0',$gpu='',$hdd='0',$hddmodel='') {
	global $DB;
	global $STATUS, $TYPE, $OS, $TASK;
	// prep customer info
	$stmt = $DB->prepare('SELECT name,phone,facebook,notes FROM custs WHERE cid=:cid LIMIT 1;');
	$stmt->bindValue(':cid',$cid,PDO::PARAM_INT);
	$stmt->execute();
	$c = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
	// prep job info
	if($jid!='') {
		$PageHeading = 'J'.$jid.' '.$TASK[$task].' for '.$model.' - C'.$cid.' '.$c['name'];
		$FormAction = '?i=J'.$jid;
	} else {
		$PageHeading = 'New Job - C'.$cid.' '.$c['name'];
		$FormAction = '?n=J&c=C'.$cid;
	}
	if($jobdate=='') {
		$jobdate = date('Y-m-d');
	}
	$StatusList = jDetailOptionList($STATUS,$status);
	$TypeList = jDetailOptionList($TYPE,$type);
	$OsList = jDetailOptionList($OS,$os);
	$TaskList = jDetailOptionList($TASK,$task);
	// output form html
	return '
	<h1>'.$PageHeading.'</h1>
	'.NavBar().'
	<form method="post" action="'.$FormAction.'">
		<input type="hidden" name="cid" value="C'.$cid.'">
		<p>
			<button type="reset">Revert</button>
			<button type="submit">Save</button>
		</p>
		<table class="first">
			<tr>
				<td>Date:</td>
				<td><input type="date" name="jobdate" value="'.$jobdate.'"></td>
				<td>Model:</td>
				<td><input type="text" name="model" value="'.$model.'" maxlength="24"></td>
			</tr>
			<tr>
				<td>Status:</td>
				<td>
					<select name="status">'.$StatusList.'</select>
				</td>
				<td>Type:</td>
				<td>
					<select name="type">'.$TypeList.'</select>
				</td>
			</tr>
			<tr>
				<td>Price:</td>
				<td><input type="number" name="price" value="'.$price.'" min="0" max="9999" step="1" class="number"> $</td>
				<td>OS:</td>
				<td>
					<select name="os">'.$OsList.'</select>
				</td>
			</tr>
			<tr>
				<td>Task:</td>
				<td>
					<select name="task">'.$TaskList.'</select>
				</td>
				<td>CPU:</td>
				<td><input type="text" name="cpu" value="'.$cpu.'" maxlength="24"></td>
			</tr>
			<tr>
				<td>Customer:</td>
				<td><a href="?i=C'.$cid.'">C'.$cid.' '.$c['name'].cNoteStar($c['notes']).'</a></td>
				<td>RAM:</td>
				<td><input type="number" name="ram" value="'.$ram.'" min="0" max="9999" step="1" class="number"> GB</td>
			</tr>
			<tr>
				<td>Contact:</td>
				<td>'.cContact($c['phone'],$c['facebook']).'</td>
				<td>GPU:</td>
				<td><input type="text" name="gpu" value="'.$gpu.'" maxlength="24"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>HDD:</td>
				<td>
					<input type="number" name="hdd" value="'.$hdd.'" min="0" max="999999" step="1" class="number"> GB
					<input type="text" name="hddmodel" value="'.$hddmodel.'" maxlength="12" class="hdd">
				</td>
			</tr>
		</table>
		<h2>Notes</h2>
		<p>
			<textarea name="notes">'.$notes.'</textarea>
		</p>
	</form>';
}

function jInfo($jid) {
	global $DB;
	// action updated job info
	if(ISSET($_POST['cid'])) {
		try {
			$stmt = $DB->prepare('UPDATE jobs SET jobdate=:jobdate, status=:status, price=:price, task=:task, notes=:notes,
				type=:type, model=:model, os=:os, cpu=:cpu, ram=:ram, gpu=:gpu, hdd=:hdd, hddmodel=:hddmodel 
				WHERE jid=:jid LIMIT 1;');
			$stmt->bindValue(':jobdate',$_POST['jobdate'],PDO::PARAM_STR);
			$stmt->bindValue(':status',$_POST['status'],PDO::PARAM_INT);
			$stmt->bindValue(':price',$_POST['price'],PDO::PARAM_INT);
			$stmt->bindValue(':task',$_POST['task'],PDO::PARAM_INT);
			$stmt->bindValue(':notes',$_POST['notes'],PDO::PARAM_STR);
			$stmt->bindValue(':type',$_POST['type'],PDO::PARAM_INT);
			$stmt->bindValue(':model',$_POST['model'],PDO::PARAM_STR);
			$stmt->bindValue(':os',$_POST['os'],PDO::PARAM_INT);
			$stmt->bindValue(':cpu',$_POST['cpu'],PDO::PARAM_STR);
			$stmt->bindValue(':ram',$_POST['ram'],PDO::PARAM_INT);
			$stmt->bindValue(':gpu',$_POST['gpu'],PDO::PARAM_STR);
			$stmt->bindValue(':hdd',$_POST['hdd'],PDO::PARAM_INT);
			$stmt->bindValue(':hddmodel',$_POST['hddmodel'],PDO::PARAM_STR);
			$stmt->bindValue(':jid',$jid,PDO::PARAM_INT);
			$stmt->execute();
		} catch(Exception $ex) {
			$page = '
			<h1>Error</h1>
			<p>
			Database Error: <code>'.$ex->getMessage().'</code>
			</p>';
			return array('Error', "error", $page);
		}
		redirect('?i=J'.$jid);
		exit;
	// show job info page
	} else {
		$stmt = $DB->prepare('SELECT jid,cid,jobdate,status,price,task,notes,type,model,os,cpu,ram,gpu,hdd,hddmodel FROM jobs WHERE jid=:jid LIMIT 1;');
		$stmt->bindValue(':jid',$jid,PDO::PARAM_INT);
		$stmt->execute();
		$j = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
		$page = jDetail($j['cid'],$jid,$j['jobdate'],$j['status'],$j['price'],$j['task'],$j['notes'],$j['type'],$j['model'],$j['os'],$j['cpu'],$j['ram'],$j['gpu'],$j['hdd'],$j['hddmodel']);
		return array('J'.$j['jid'].' '.$j['model'], "job", $page);
	}
}

function jNew($cid) {
	// action new job info
	if(ISSET($_POST['cid'])) {
		global $DB;
		try {
			$stmt = $DB->query('SELECT jid FROM jobs ORDER BY jid DESC LIMIT 1;');
			$stmt->execute();
			$j = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
			$stmt = $DB->prepare('INSERT INTO jobs (jid, cid, jobdate, status, price, task, notes, type, model, os, cpu, ram, gpu, hdd, hddmodel) VALUES 
				(:jid, :cid, :jobdate, :status, :price, :task, :notes, :type, :model, :os, :cpu, :ram, :gpu, :hdd, :hddmodel);');
			$stmt->bindValue(':jid',($j['jid']+1),PDO::PARAM_INT);
			$stmt->bindValue(':cid',$cid,PDO::PARAM_INT);
			$stmt->bindValue(':jobdate',$_POST['jobdate'],PDO::PARAM_STR);
			$stmt->bindValue(':status',$_POST['status'],PDO::PARAM_INT);
			$stmt->bindValue(':price',$_POST['price'],PDO::PARAM_INT);
			$stmt->bindValue(':task',$_POST['task'],PDO::PARAM_INT);
			$stmt->bindValue(':notes',$_POST['notes'],PDO::PARAM_STR);
			$stmt->bindValue(':type',$_POST['type'],PDO::PARAM_INT);
			$stmt->bindValue(':model',$_POST['model'],PDO::PARAM_STR);
			$stmt->bindValue(':os',$_POST['os'],PDO::PARAM_INT);
			$stmt->bindValue(':cpu',$_POST['cpu'],PDO::PARAM_STR);
			$stmt->bindValue(':ram',$_POST['ram'],PDO::PARAM_INT);
			$stmt->bindValue(':gpu',$_POST['gpu'],PDO::PARAM_STR);
			$stmt->bindValue(':hdd',$_POST['hdd'],PDO::PARAM_INT);
			$stmt->bindValue(':hddmodel',$_POST['hddmodel'],PDO::PARAM_STR);
			$stmt->execute();
		} catch(Exception $ex) {
			$page = '
			<h1>Error</h1>
			<p>
			Database Error: <code>'.$ex->getMessage().'</code>
			</p>';
			return array('Error', "error", $page);
		}
		redirect('?i=J'.($j['jid']+1));
		exit;
	// show new job page
	} else {
		return array('New Job', "job", jDetail($cid));
	}
}

?>