<?
$save_path = getcwd() . '/result/'; // Путь к директории на сервере от /. Директория должна существовать и быть доступна для записи.
$file_list = array ( // Массив с фидами и параметрами их обработки
        array (
            'feed_type' => 'ozon', // Тип фида для конвертации: 'ozon' - Ozon, 'megamarket' - Мегамаркет
            'file_feed' => 'http://mptls.vasiliykvasov.ru/ym-yml-converter/example.xml', // Ссылка на удаленный файл
            'option_prices' => true, // Обновлять цены: true — да, false - нет
            'option_outlets' => true, // Обновлять остатки: true — да, false - нет
            'warehouse_id' => 'sklad', // Идентификатор склада для обновления остатков. Если не используется, нужно оставить ''
            'save_name' => 'example-ozon.xml' // Название файла при сохранении
        ),
        array (
            'feed_type' => 'megamarket', // Тип фида для конвертации: 'ozon' - Ozon, 'megamarket' - Мегамаркет
            'file_feed' => getcwd() . '/example.xml', // Ссылка на локальный файл
            'option_prices' => true, // Обновлять цены: true — да, false - нет
            'option_outlets' => true, // Обновлять остатки: true — да, false - нет
            'warehouse_id' => '0', // Идентификатор склада для обновления остатков. Если не используется, нужно оставить ''
            'save_name' => 'example-megamarket.xml' // Название файла при сохранении
        )
    );
include 'converter.php';
foreach ($file_list as $feed) { // Для каждого элемента массива с настройками
    convert_yml($feed['feed_type'], $save_path, $feed['file_feed'], $feed['option_prices'], $feed['option_outlets'], $feed['warehouse_id'], $feed['save_name']); // Вызвать функцию из script.php
}