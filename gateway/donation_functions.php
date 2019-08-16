<?php

function tableExists($tableName) { 
	global $_CONFIG, $db;
	$sql ='SHOW TABLES WHERE Tables_in_' . $_CONFIG['database'] . ' = \'' . $tableName . '\'';
	$rs = mysql_query($sql);

	if(!mysql_fetch_array($rs)) {
		return false;
	} else {
		return true;
	}
}
function getUsername($user_id) {
	global $db;
	$select = $db->query("SELECT username FROM users WHERE userid = ".$user_id);
	if($db->num_rows($select)) {
		$data = $db->fetch_row($select);
		return $data['username'];	
	}
}
function getAddress() {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
    return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
function centsToDollar($cents) { 
	return number_format(($cents/100),2,'.',''); 
}
function dollarToCents($dol) { 
	return number_format(($dol*100),0,'.',''); 
}
function getSettings() {
	global $db;
	$select = $db->query("SELECT * FROM donate_settings");
	$dset = '';
	while($s = $db->fetch_row($select)) {
		$dset[$s['donate_name']] = $s['donate_value'];
	}
	return $dset;
}
function creditPack($pack, $user=0, $qty=1) {
	global $db, $userid;
	
	$dset = getSettings();
	$user = (isset($user) ? $user : $userid);
	$qty = (isset($qty) && $qty >= 1 ? $qty : 1);
	
	$pack_check = $db->query("SELECT * FROM donate WHERE pack_id = ".$pack);
	if($db->num_rows($pack_check)) {
		
		$user_check = $db->query("SELECT userid FROM users WHERE userid=".$user);
		if($db->num_rows($user_check)) {
			
			$pack = $db->fetch_row($pack_check);
			
			if($dset['credit_type'] == 1) {
				
				item_add($user, $pack['pack_item_id'], $qty);
				
			} else {
				
				for ($i = 1; $i <= $qty; $i++) {
					$getBenefits = @unserialize($pack['pack_benefits']);
					$getItems = @unserialize($pack['pack_items']);
					
					if($getBenefits != false) {
						$user_stats = array('strength','agility','guard','labour','IQ');
						foreach($getBenefits as $ben) {
							$db->query("UPDATE ".(in_array($ben[0],$user_stats) ? 'userstats' : 'users')." SET ".$ben[0]." = ".(isset($ben[1]) && ($ben[1] == '+' || $ben[1] == '-') ? $ben[0].' '.$ben[1].' '.$ben[2] : $ben[2])." WHERE userid = ".$user);	
						}
					}
					if($getItems != false) {
						foreach($getItems as $item) {				
							item_add($user, $item[0], $item[1]);
						}
					}	
				}
			}		
		}
	}
}

function list_gateways() {
	if($handle = opendir('gateway')) {		
		while (false !== ($file = readdir($handle))) {
			if(file_exists('gateway/'.$file.'/info.xml')) {
				$gateways[] = $file;				
			}
		}	
	}	
	return $gateways;
}
function benefitList($selected=0) {
	global $db;
	$data = '<select name="benefits">';
	$tables = array('users','userstats');
	$blocked = array('userid','userpass','username');
	foreach($tables as $t) {
		$user_result = $db->query("SHOW COLUMNS FROM ".$t);
		if($user_result && $db->num_rows($user_result)) {
			while($row = $db->fetch_row($user_result)) {
				if(!in_array($row['Field'], $blocked)) {
					$data .= '<option value="'.$row['Field'].'" '.($selected == $row['Field'] && $selected ? 'selected="selected"' : '').' name="'.$t.'">'.$row['Field'].'</option>';	
				}
			}
		}
	}
	$data .= '</select>';	
	return $data;
}
function load_gateway($gateway) {
	if(file_exists('gateway/'.$gateway.'/info.xml')) {
		
		$load_file = simplexml_load_file('gateway/'.$gateway.'/info.xml');
		
		if($load_file->getName() == 'settings') {
			foreach($load_file->children() as $child) {
				$data[$child->getName()] = $child;
			}	
		}
	}
	return $data;
}
function unserializeBenefits($data) {
	$data = @unserialize($data);
	if($data !== false) {
		$return = '';
		$return .= '<ul>';
		foreach($data as $info) {
			$type = $info[0];
			$value = ($info[0] == 'money' ? '$' : '').(ctype_digit($info[2]) ? number_format($info[2]) : $info[2]);
			
			$first = array('donatordays','level','exp','maxhp');
			$replace = array('Donator Days','Levels','EXP','Maxium HP');
			$type = ucfirst(str_replace($first, $replace, $type));
				
			$return .= '<li>
				'.($info[1] == '-' ? '- ' : '').(ctype_digit($value) ? number_format($value) : $value).' '.$type.'
			</li>';	
		}
		$return .= '</ul>';
		return $return;
	} else {
		return '<center>This pack has no benefits.</center>';	
	}
}
function unserializeItems($data) {
	global $db;
	if($data) {
		$data = @unserialize($data);
		if($data !== false) {
			$return = '';
			$return .= '<ul>';
			foreach($data as $info) {
				$itm_id = abs((int) $info[0]);
				$qty = abs((int) $info[1]);
				
				$select = $db->query('SELECT itmname FROM items WHERE itmid = '.$itm_id);
				if($db->num_rows($select)) {
					$item = $db->fetch_row($select);
					$return .= '<li>
						<a href="iteminfo.php?ID='.$itm_id.'" target="_blank">'.$item['itmname'].'</a> [x'.$qty.']
					</li>';	
				}
			}
			$return .= '</ul>';
			return $return;
		} else {
			return '<center>This pack has no items.</center>';	
		}	
	} else {
		return '<center>This pack has no items.</center>';	
	}
}
?>