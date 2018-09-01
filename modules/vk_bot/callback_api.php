<?php

chdir(dirname(__FILE__) . '/../../');
require_once './lib/vk_api.lib.php';
require_once './lib/vk_bot.addon.php';
include_once("./config.php");
//include_once("./lib/loader.php");

if (!isset($_REQUEST)) {
  exit;
}

callback_handleEvent();

function callback_handleEvent() {
  $event = _callback_getEvent();

  try {
    switch ($event['type']) {
      //Подтверждение сервера
      case 'confirmation':
        _callback_handleConfirmation();
        break;

      //Получение нового сообщения
      case 'message_new':
        _callback_handleMessageNew($event['object']);
        break;      
	  case 'message_reply':
        _callback_response('ok');
        break;

      default:
        _callback_response('Unsupported event');
        break;
    }
  } catch (Exception $e) {
    log_error($e);
  }

  _callback_okResponse();
}

function _callback_getEvent() {
  return json_decode(file_get_contents('php://input'), true);
}

function _callback_handleConfirmation() {
  _callback_response(CALLBACK_API_CONFIRMATION_TOKEN);
}

function _callback_handleMessageNew($data) {
  file_get_contents('http://localhost/command.php?qry='.urlencode($data['text']));
  /*$user_id = $data['peer_id'];
  bot_sendMessage($user_id, $data);*/
  _callback_okResponse();
}

function _callback_okResponse() {
  _callback_response('ok');
}

function _callback_response($data) {
  echo $data;
  exit();
}


