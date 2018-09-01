<?php
/**
* vk_bot 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 12:09:01 [Sep 01, 2018])
*/
//
//
class vk_bot extends module {
/**
* vk_bot
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="vk_bot";
  $this->title="Вконтакте";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['API_KEY']=$this->config['API_KEY'];
 $out['CALLBACK']=$this->config['CALLBACK'];
 $out['MSGLVL']=$this->config['MSGLVL'];
 if ($this->view_mode=='update_settings') {
   global $api_key;
   $this->config['API_KEY']=$api_key;
   global $callback;
   $this->config['CALLBACK']=$callback;
   global $msglvl;
   $this->config['MSGLVL']=$msglvl;

   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='app_vkbot' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_app_vkbot') {
   $this->search_app_vkbot($out);
  }
  if ($this->view_mode=='edit_app_vkbot') {
   $this->edit_app_vkbot($out, $this->id);
  }
  if ($this->view_mode=='delete_app_vkbot') {
   $this->delete_app_vkbot($this->id);
   $this->redirect("?");
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* app_vkbot search
*
* @access public
*/
 function search_app_vkbot(&$out) {
  require(DIR_MODULES.$this->name.'/app_vkbot_search.inc.php');
 }
/**
* app_vkbot edit/add
*
* @access public
*/
 function edit_app_vkbot(&$out, $id) {
  require(DIR_MODULES.$this->name.'/app_vkbot_edit.inc.php');
 }
/**
* app_vkbot delete record
*
* @access public
*/
 function delete_app_vkbot($id) {
  $rec=SQLSelectOne("SELECT * FROM app_vkbot WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM app_vkbot WHERE ID='".$rec['ID']."'");
 }
 function processSubscription($event, $details='') {
 $this->getConfig();
  if ($event=='SAY') {
   $level=$details['level'];
   $message=$details['message'];
   if ($level>=$this->config['MSGLVL']) {
		bot_sendMessage(gg('vk_answer_id'), $message);
   }
   //...
  }
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  subscribeToEvent($this->name, 'SAY');
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS app_vkbot');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
app_vkbot - 
*/
  $data = <<<EOD
 app_vkbot: ID int(10) unsigned NOT NULL auto_increment
 app_vkbot: TITLE varchar(100) NOT NULL DEFAULT ''
 app_vkbot: COLOR varchar(255) NOT NULL DEFAULT ''
 app_vkbot: VAL varchar(255) NOT NULL DEFAULT ''
 app_vkbot: CODE varchar(255) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgU2VwIDAxLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
