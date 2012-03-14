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
include "root.php";

//define the dialplan class
	if (!class_exists('dialplan')) {
		class dialplan {
			//variables
			public $result;
			public $domain_uuid;
			public $dialplan_uuid;

			//dialplans
			public $dialplan_name;
			public $dialplan_continue;
			public $dialplan_order;
			public $dialplan_context;
			public $dialplan_enabled;
			public $dialplan_description;

			//dialplan_details
			public $dialplan_detail_tag;
			public $dialplan_detail_order;
			public $dialplan_detail_type;
			public $dialplan_detail_data;
			public $dialplan_detail_break;
			public $dialplan_detail_inline;
			public $dialplan_detail_group;

			function dialplan_add() {
				global $db;
				$dialplan_uuid = uuid();
				$sql = "insert into v_dialplans ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_uuid, ";
				$sql .= "dialplan_name, ";
				$sql .= "dialplan_continue, ";
				$sql .= "dialplan_order, ";
				$sql .= "dialplan_context, ";
				$sql .= "dialplan_enabled, ";
				$sql .= "dialplan_description ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'".$this->domain_uuid."', ";
				$sql .= "'".$this->dialplan_uuid."', ";
				$sql .= "'".$this->dialplan_name."', ";
				$sql .= "'".$this->dialplan_continue."', ";
				$sql .= "'".$this->dialplan_order."', ";
				$sql .= "'".$this->dialplan_context."', ";
				$sql .= "'".$this->dialplan_enabled."', ";
				$sql .= "'".$this->dialplan_description."' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			} //end function

			function dialplan_update() {
				global $db;
				$sql = "update v_dialplans set ";
				$sql .= "dialplan_name = '".$this->dialplan_name."', ";
				if (srlen($this->dialplan_continue) > 0) {
					$sql .= "dialplan_continue = '".$this->dialplan_continue."', ";
				}
				$sql .= "dialplan_order = '".$this->dialplan_order."', ";
				$sql .= "dialplan_context = '".$this->dialplan_context."', ";
				$sql .= "dialplan_enabled = '".$this->dialplan_enabled."', ";
				$sql .= "dialplan_description = '".$this->dialplan_description."' ";
				$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
				$sql .= "and dialplan_uuid = '".$this->dialplan_uuid."' ";
				//echo "sql: ".$sql."<br />";
				$db->query($sql);
				unset($sql);
			}

			function dialplan_detail_add() {
				global $db;
				$dialplan_detail_uuid = uuid();
				$sql = "insert into v_dialplan_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_uuid, ";
				$sql .= "dialplan_detail_tag, ";
				$sql .= "dialplan_detail_order, ";
				$sql .= "dialplan_detail_type, ";
				$sql .= "dialplan_detail_data, ";
				$sql .= "dialplan_detail_break, ";
				$sql .= "dialplan_detail_inline, ";
				$sql .= "dialplan_detail_group ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'".$this->domain_uuid."', ";
				$sql .= "'".$this->dialplan_uuid."', ";
				$sql .= "'".$this->dialplan_detail_tag."', ";
				$sql .= "'".$this->dialplan_detail_order."', ";
				$sql .= "'".$this->dialplan_detail_type."', ";
				$sql .= "'".$this->dialplan_detail_data."', ";
				$sql .= "'".$this->dialplan_detail_break."', ";
				$sql .= "'".$this->dialplan_detail_inline."', ";
				if (strlen($this->dialplan_detail_group) == 0) {
					$sql .= "null ";
				}
				else {
					$sql .= "'".$this->dialplan_detail_group."' ";
				}
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			} //end function

			function dialplan_detail_update() {
				global $db;
				$sql = "update v_dialplans set ";
				$sql .= "dialplan_detail_order = '".$this->dialplan_detail_order."', ";
				$sql .= "dialplan_detail_type = '".$this->dialplan_detail_type."', ";
				$sql .= "dialplan_detail_data = '".$this->dialplan_detail_data."' ";
				if (srlen($this->dialplan_detail_break) > 0) {
					$sql .= "dialplan_detail_break = '".$this->dialplan_detail_break."', ";
				}
				if (srlen($this->dialplan_detail_inline) > 0) {
					$sql .= "dialplan_detail_inline = '".$this->dialplan_detail_inline."', ";
				}
				if (srlen($this->dialplan_detail_group) > 0) {
					$sql .= "dialplan_detail_group = '".$this->dialplan_detail_group."', ";
				}
				$sql .= "dialplan_detail_tag = '".$this->dialplan_detail_tag."' ";
				$sql .= "where domain_uuid = '".$this->domain_uuid."' ";
				$sql .= "and dialplan_uuid = '".$this->dialplan_uuid."' ";
				//echo "sql: ".$sql."<br />";
				$db->query($sql);
				unset($sql);
			} //end function

			function restore_advanced_xml() {
				$switch_dialplan_dir = $this->switch_dialplan_dir;
				//get the contents of the dialplan/default.xml
					$file_default_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/templates/conf/dialplan/default.xml';
					$file_default_contents = file_get_contents($file_default_path);
				//prepare the file contents and the path
					if (count($_SESSION['domains']) < 2) {
						//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
							$file_default_contents = str_replace("{v_domain}", 'default', $file_default_contents);
						//set the file path
							$file_path = $switch_dialplan_dir.'/default.xml';
					}
					else {
						//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
							$file_default_contents = str_replace("{v_domain}", $_SESSION['domain_name'], $file_default_contents);
						//set the file path
							$file_path = $switch_dialplan_dir.'/'.$_SESSION['domain_name'].'.xml';
					}
				//write the default dialplan
					$fh = fopen($file_path,'w') or die('Unable to write to '.$file_path.'. Make sure the path exists and permissons are set correctly.');
					fwrite($fh, $file_default_contents);
					fclose($fh);
				//set the message
					$this->result['dialplan']['restore']['msg'] = "Default Restored";
			}
		}
	}
?>