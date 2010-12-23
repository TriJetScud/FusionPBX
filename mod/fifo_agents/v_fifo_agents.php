<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";


//$time_start = microtime(true);
//sleep for a while
//usleep(1000000);


$orderby = $_GET["orderby"];
$order = $_GET["order"];


	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";


	echo "<table width='100%' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%' nowrap='nowrap' align='left'><b>Active Agents</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2' align='left'>\n";
	echo "Shows the agents that are currently logged into the queues.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</tr></table>\n";

	//set the default order by and order asc, desc
		if (strlen($orderby) == 0) { 
			$orderby  = 'fifo_name';
			$order = 'asc';
		}

	//run the sql queries
		$sql = "";
		$sql .= " select * from v_fifo_agents ";
		if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
		//$sql .= " limit $rowsperpage offset $offset ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		$resultcount = count($result);
		unset ($prepstatement, $sql);


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorderby('fifo_name', 'Queue Name', $orderby, $order);
	echo thorderby('agent_username', 'Username', $orderby, $order);
	echo thorderby('agent_priority', 'Agent Priority', $orderby, $order);
	echo thorderby('agent_status', 'Status', $orderby, $order);
	echo thorderby('agent_last_call', 'Last Call', $orderby, $order);
	echo thorderby('agent_last_uuid', 'Last UUID', $orderby, $order);
	echo thorderby('agent_contact_number', 'Contact Number', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_fifo_agents_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	//echo "	<input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_fifo_agents_edit.php'\" value='+'>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//set the php variables
				$agent_last_call = $row[agent_last_call];
				$agent_status = $row[agent_status];

			//format the last call time
				if ($agent_last_call == 0) {
					$agent_last_call_desc = '';
				}
				else {
					$agent_last_call_desc = date("g:i:s a j M Y",$agent_last_call);
				}

			//get the agent status session array
				//unset($_SESSION["array_agent_status"]);
				if (!is_array($_SESSION["array_agent_status"])) {
					$sql = "SELECT var_name, var_value FROM v_vars ";
					$sql .= "where v_id = '$v_id' ";
					$sql .= "and var_cat = 'Queues Agent Status' ";
					$prepstatement = $db->prepare(check_sql($sql));
					$prepstatement->execute();
					$result = $prepstatement->fetchAll();
					foreach($result as $field) {
						$_SESSION["array_agent_status"][$field[var_value]] = $field[var_name];
					}
				}

			//get the agent description
				$agent_status_desc = $_SESSION["array_agent_status"][$agent_status];

			echo "<tr >\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[fifo_name]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[agent_username]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[agent_priority]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$agent_status_desc."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$agent_last_call_desc."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[agent_last_uuid]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[agent_contact_number]."</td>\n";
			echo "	<td valign='top' align='right'>\n";
			echo "		<a href='v_fifo_agents_edit.php?id=".$row[fifo_agent_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' alt='edit' border='0'></a>\n";
			echo "		<a href='v_fifo_agents_delete.php?id=".$row[fifo_agent_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' alt='delete' border='0'></a>\n";
			//echo "		<input type='button' class='btn' name='' alt='edit' onclick=\"window.location='v_fifo_agents_edit.php?id=".$row[fifo_agent_id]."'\" value='e'>\n";
			//echo "		<input type='button' class='btn' name='' alt='delete' onclick=\"if (confirm('Are you sure you want to delete this?')) { window.location='v_fifo_agents_delete.php?id=".$row[fifo_agent_id]."' }\" value='x'>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results


	echo "<tr>\n";
	echo "<td colspan='8' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	//echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_fifo_agents_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	//echo "		<input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_fifo_agents_edit.php'\" value='+'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

//sleep(1);
//$time_end = microtime(true);
//$time = $time_end - $time_start;
//if ($time < 2) {
//	echo "use cache ";
//}
//else {
//	echo "expired the cache ";
//}
//echo "load time $time seconds\n";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
