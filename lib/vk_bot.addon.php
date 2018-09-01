<?php

function bot_sendMessage($user_id, $msg=false, $photo=false) {
  $users_get_response = vkApi_usersGet($user_id);
  $user = array_pop($users_get_response);
  if(!$msg) {
  $msg = "{$user['first_name']}, ваша команда принята!";
  }
  $attachments = array();
  if($photo) {
	  $photo = _bot_uploadPhoto($user_id, $photo);
	  $attachments = array(
		'photo'.$photo['owner_id'].'_'.$photo['id'],
	  );
  }
  vkApi_messagesSend($user_id, $msg, $attachments);
}

function _bot_uploadPhoto($user_id, $file_name) {
  $upload_server_response = vkApi_photosGetMessagesUploadServer($user_id);
  $upload_response = vkApi_upload($upload_server_response['upload_url'], $file_name);

  $photo = $upload_response['photo'];
  $server = $upload_response['server'];
  $hash = $upload_response['hash'];

  $save_response = vkApi_photosSaveMessagesPhoto($photo, $server, $hash);
  $photo = array_pop($save_response);

  return $photo;
}
