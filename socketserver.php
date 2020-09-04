#!/usr/bin/env php

<?php
function go(){
	$starttime = round(microtime(true),2);
	echo "GO() ... \r\n";

	echo "socket_create ...";
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

	if($socket < 0){
		echo "Error: ".socket_strerror(socket_last_error())."\r\n";
		exit();
	} else {
	    echo "OK \r\n";
	}
	

	echo "socket_bind ...";
	$bind = socket_bind($socket, '0.0.0.0', 8888);//привязываем его к указанным ip и порту
	if($bind < 0){
	    echo "Error: ".socket_strerror(socket_last_error())."\r\n";
		exit();
	}else{
	    echo "OK \r\n";
	}	
	
	socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);//разрешаем использовать один порт для нескольких соединений

	echo "Listening socket... ";
	$listen = socket_listen($socket, 5);//слушаем сокет

	if($listen < 0){
	    echo "Error: ".socket_strerror(socket_last_error())."\r\n";
		exit();
	}else{
	    echo "OK \r\n";
	}
   
	while(true){ //Бесконечный цикл ожидания подключений
		
        echo "Waiting... ";
	    $accept = @socket_accept($socket); //Зависаем пока не получим ответа
	    if($accept === false){
	        echo "Error: ".socket_strerror(socket_last_error())."\r\n";
		    usleep(100);
	    } else {
	        echo "OK \r\n";
	        echo "Client \"".$accept."\" has connected\r\n";
		}
        echo "cliend says: ".socket_read($accept,512);
        $msg =  "Hello, Client!";
	    echo "Send to client \"".$msg."\"... ";
	    socket_write($accept, $msg);
	    echo "OK \r\n";
		
		//ограничение времени работы
		/*
        if( ( round(microtime(true),2) - $starttime) > 100) { 
			echo "time = ".(round(microtime(true),2) - $starttime); 
			echo "return <br />\r\n"; 
			return $socket;
		}
        */


	}


}

 if(extension_loaded('sockets')){
	echo "WebSockets OK";
 } else{
	echo "WebSockets UNAVAILABLE"; exit();
 }
  

error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);		//Время выполнения скрипта не ограничено
ob_implicit_flush();	//Включаем вывод без буферизации 

$socket = go();			//Функция с бесконечным циклом, возвращает $socket по запросу выполненному по прошествии 100 секнуд. 

echo "go() ended\r\n";

if (isset($socket)){
    echo "Closing connection... ";
	@socket_shutdown($socket);
    socket_close($socket);
    echo "OK \r\n";
}

