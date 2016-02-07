<?php

if(count(json_decode($_POST['mandrill_events'])) > 0) {
  foreach(json_decode($_POST['mandrill_events']) as $event) {

    // Set url and standard fields
    $url = 'http://www.google-analytics.com/collect?';
    $fields = array(
      'v'   => 1,
      'tid' => 'UA-XXXXXXXX-Y',                    // REPLACE with your Google Analytics web property UA code
      'cid' => time() . '-' . rand(100000,999999),
      'dh'  => 'www.example.com',                  // REPLACE with your website domain or delete this line
      't'   => 'event',
      'ec'  => 'email',
      'ea'  => $event->event,
      'ni'  => 1                                   // Non-interactive event
      );

    // Prepare custom metric. Value is always 1, but key is not always the same.
    if($event->event == 'send') {
      $fields['cm1'] = 1;
    } elseif($event->event == 'open') {
      $fields['cm2'] = 1;
    } elseif($event->event == 'click') {
      $fields['cm3'] = 1;
    } elseif($event->event == 'spam') {
      $fields['cm4'] = 1;
    } elseif($event->event == 'hard_bounce') {
      $fields['cm5'] = 1;
    }

    // Prepare event label
    if(isset($event->msg->metadata->email_type)) {
      $fields['el'] = $event->msg->metadata->email_type;
    }

    // Prepare user_id
    if(isset($event->msg->metadata->user_id)) {
      $fields['uid'] = $event->msg->metadata->user_id;
    }

    // Prepare ip
    if(isset($event->ip)) {
      $fields['uip'] = $event->ip;
    }

    // Prepare user_agent
    if(isset($event->user_agent)) {
      $fields['ua'] = $event->user_agent;
    }

    // Build URL
    if(count($fields) > 0) {
      $fields_string = '';
      foreach($fields as $key => $value) {
        $fields_string .= $key . '=' . urlencode($value) . '&';
      }
      $fields_string = rtrim($fields_string,'&');
    }

    // Send the post request to Google Analytics
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
    $result = curl_exec($ch);

  }
}