<?php 
	// $to = 'info@aipr-rf.ru, saloev.saadi@yandex.com, qazaqon2@gmail.com, Rakhimov.f@ya.ru'; // note the comma
	$to = 'info@aipr-rf.ru'; // note the comma
	// $to = 'kazakon2@mail.ru'; // note the comma

	// Subject
  $rest_json = file_get_contents("php://input");
  $params = json_decode($rest_json, true);
  $title = $params['title'];
  $phone = $params['phone'];
	$name = $params['name'];
	$email = $params['email'];
	$isNotFound = $params['isNotFound'];
  $subject = $isNotFound ? 'Заявка на исследование с сайта aipr-rf.ru': 'Заказ исследования с сайта aipr-rf.ru';


	// Message
	$message = "
	<html>
	<head>
		<title>$subject</title>
	</head>
	<body>
    <p>Исследование: $title</p>
    <p>Номер телефона: $phone</p>
    <p>Email: $email</p>
    <p>Имя : $name</p>
	</body>
	</html>
	";
	
	// To send HTML mail, the Content-type header must be set
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=utf-8';

	
	// Mail it
	if (mail($to, $subject, $message, implode("\r\n", $headers))) {
    echo "Email send";
  } else {
    echo "Error";
  }