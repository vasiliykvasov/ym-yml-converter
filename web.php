<?
include 'converter.php';
// Получение данных из формы
$warehouse_id = 'none'; // Задаем складу идентификатор по-умолчанию
if (isset($_GET['option_outlets']) && $_GET['option_outlets'] === 'on') { // Если обновление остатков включено
    $option_outlets = true; // Обновление остатков включено
    if (empty($_GET['warehouse_id'])) { // Если не передан идентификатор склада
        $warehouse_id = '0'; // Задаем складу идентификатор 0
    } else {
        $warehouse_id = strval($_GET['warehouse_id']); // Задаем складу переданный идентификатор
    }
} else { // Иначе
    $option_outlets = false; // Обновление остатков выключено
}
if (isset($_GET['option_prices']) && $_GET['option_prices'] === 'on') { // Если обновление цен включено
    $option_prices = true; // Обновление цен включено
} else { // Иначе
    $option_prices = false; // Обновление цен выключено
}
if (isset($_GET['option_view']) && $_GET['option_view'] === 'on') { // Если отладка включена
    $option_save = 'view'; // Просмотр файла
} else { // Иначе
    $option_save = 'download'; // Скачивание файла
}

if (isset($_GET['feed_type'])) {
    // echo($_GET['feed_type']);
} else {
    // echo('no file');
}

if (isset($_GET['file_feed']) && (!empty($_GET['file_feed']))) { // Если удаленный файл передан
    if (!isset($_GET['save_name']) || empty($_GET['save_name'])) { // Если не указано название при сохранении
        $option_savename = basename($_GET['file_feed']); // Название файла будет названием удаленного файла
    } else { // Иначе
        $option_savename = $_GET['save_name'] . '.xml'; // Название файла будет, как указано при сохранении
    }
    if (fopen($_GET['file_feed'], "r")) {
        convert_yml($_GET['feed_type'], $option_save, $_GET['file_feed'], $option_prices, $option_outlets, $warehouse_id, $option_savename); // Запустить функцию обновления
    } else {
        echo('Неверная ссылка на фид!');
    }
}