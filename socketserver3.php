#!/usr/bin/env php

<?php
    error_reporting(E_ALL);
    set_time_limit(0); //Скрипт должен работать постоянно
    ob_implicit_flush(); //Все echo должны сразу же отправляться клиенту
    $address = '0.0.0.0'; //Адрес работы сервера
    $port = 8888; //Порт работы сервера (лучше какой-нибудь редкоиспользуемый)
    $clients = [];
    if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
        //AF_INET - семейство протоколов
        //SOCK_STREAM - тип сокета
        //SOL_TCP - протокол
        echo "Socket create error".PHP_EOL;
    }
    else {
        echo "Socket created".PHP_EOL;
    }
    //Связываем дескриптор сокета с указанным адресом и портом
    if (($ret = socket_bind($sock, $address, $port)) < 0) {
        echo "Socket communication error with address and port".PHP_EOL;
    }
    else {
        echo sprintf("Socket binds successfully to address %s and port %s".PHP_EOL, $address, $port);
    }
    //Начинаем прослушивание сокета (максимум 10 одновременных соединений)
    if (($ret = socket_listen($sock, 10)) < 0) {
        echo "Ошибка при попытке прослушивания сокета".PHP_EOL;
    }
    else {
        echo "Ждём подключение клиента".PHP_EOL;
    }
    do {
        //Принимаем соединение с сокетом
        if (($client = socket_accept($sock)) < 0) {
            echo "Ошибка при старте соединений с сокетом".PHP_EOL;
        } else {
            echo "Сокет готов к приёму сообщений".PHP_EOL;
            $clients[] = $client;
            $msg = "Hello!"; //Сообщение клиенту
            echo "Сообщение от сервера: $msg".PHP_EOL;
            socket_write($client, $msg, strlen($msg)); //Запись в сокет
        }
        //Бесконечный цикл ожидания клиентов
        do {
          echo 'Сообщение от клиента: ';
          if (false === ($buf = socket_read($client, 1024))) {
              echo "Ошибка при чтении сообщения от клиента".PHP_EOL;       }
          else {
              echo $buf.PHP_EOL; //Сообщение от клиента
          }
          //Если клиент передал exit, то отключаем соединение
          if (trim(strval($buf)) === 'exit') {
            socket_close($client);
            break 1;
          }

        //Если клиент передал stop, то отключаем соединение и останавливаем сервер
        if (trim(strval($buf)) === 'stop') {
            socket_close($client);
            break 2;
        }
          socket_write($client, $buf, strlen($buf));
        } while (true);
    } while (true);
    //Останавливаем работу с сокетом
    if (isset($sock)) {
    socket_close($sock);
    echo "Сокет успешно закрыт".PHP_EOL;
    }
?>