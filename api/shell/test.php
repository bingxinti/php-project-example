<?php
include './shell.php';

$wechat = W2Wechat::getInstance(W2Wechat::SERVER_TYPE);
$res = $wechat->getShortUrl('http://njart-img.dawennet.com/upload/admin/77/37c95f667fa43273f22ef0f45d565e0f.png');
var_dump($res);
var_dump($wechat);



// /usr/bin/nohup /usr/bin/php /data/web/njart/api/shell/send_ticket_sms_add.php > /data/web/njart/api/shell/logs/send_ticket_sms_add.log 2>&1 &
// /usr/bin/nohup /usr/bin/php /data/web/njart/api/shell/send_ticket_sms.php > /data/web/njart/api/shell/logs/send_ticket_sms.log 2>&1 &