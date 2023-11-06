<?
function outlets_sber() { // Изначальная функция countToOutlets для Сбера
    // Запись логов
    $log_file = "sber-logs.txt";
    $log_text = "История действий " . date('j.m.Y H:i:s') . "\n\n";
    $log_echo = true;
    // Входные данные
    $file_list = array( // Массив с файлами
        "https://mptls.vasiliykvasov.ru/ym-yml-converter/test.xml"
    );
    // Данные фида
    $warehouse_id = "1"; // Идентификатор склада
    // Данные для сохранения
    $file_output_directory = getcwd() . "/outlets-sber/"; // Директория сохранения файла
    $file_mod = "sber"; // Префикс для модифицированного файла
    // Проверяем, можно ли сохранить файл в указанную директорию
    $file_perms = substr(sprintf('%o', fileperms($file_output_directory)), -4); // Запрашиваем права доступа к файлу
    if (is_writable($file_output_directory)) { // Если запись в директорию сохранения файла доступна
        $log_text .= "Запись в директорию " . $file_output_directory . " доступна. Права доступа " . $file_perms . ".\n\n"; // Выводим уведомление, что запись в     директорию доступна
        // Действия с элементами
        foreach ($file_list as $file) { // С каждым файлом в списке файлов делаем следующее
            if (fopen($file, "r")) { // Проверяем существование удаленного файла
                $log_text .= "Файл " . $file . " получен.\n"; // Выводим уведомление, что файл получен
                $xml = simplexml_load_file($file); // Получаем XML
                foreach ($xml->shop->offers->offer as $element_offer) { // Каждый offer записываем в переменную element_offer
                    $element_count = $element_offer->count; // Записываем количество остатков
                    $element_id = $element_offer["id"]; // Записываем ID
                    $element_name = $element_offer->name; // Записываем наименование
                    if (empty($element_count)) { // Если остатков нет
                        $log_text .= "[" . $element_id . "] " . $element_name . " нет в остатках.\n"; // Выводим уведомления, что у элемента нет остатков
                    } else { // Если остатки есть
                        $element_outlets = $element_offer->addChild('outlets'); // Создаем элемент <outlets>
                        $element_outlet = $element_outlets->addChild('outlet'); // Создаем элемент <outlet>
                        $element_outlet->addAttribute('id', $warehouse_id); // Добавляем элементу <outlets> свойство id со значением идентификатора склада
                        $element_outlet->addAttribute('instock', $element_count); // Добавляем элементу <outlets> свойство instock со значением количества остатков
                        $log_text .= "[" . $element_id . "] " . $element_name . ": " . $element_count . " на складе " . $warehouse_id . ".\n"; // Выводим     уведомление,     об остатках элемента на складе
                    }
                }
                $file_output = $file_output_directory . $file_mod ."-" . basename($file); // Создаем новое имя файла
                if (is_writable($file_output_directory)) { // Если запись в директорию сохранения файла доступна
                    $xml->asXML($file_output); // Сохраняем XML файл
                    $log_text .= "Файл " . $file_output . " записан." . "\n\n"; // Выводим уведомление, что файл записан
                } else { // Если запись в директорию сохранения файла недоступна
                    $log_text .= "Файл " . $file_output . " не записан." . "\n\n";
                }
            } else { // Если удаленного файла нет
                $log_text .= 'Не удалось открыть файл ' . $file . ".\n\n"; // Выводим уведомление, что не удалось открыть файл
            }
        }
        file_put_contents ($file_output_directory . $log_file, $log_text); // Записываем логи в файл
        if ($log_echo) {
            echo "Логи записаны в " . $file_output_directory . $log_file; // Выводим уведомление об успешной записи
        }
    } else { // Если запись в директорию сохранения файла не доступна
        $log_text .= "Запись в директорию " . $file_output_directory . " недоступна. Права доступа " . $file_perms . ".\n"; // Выводим уведомление, что запись     в директорию файла недоступна
        mkdir($file_output_directory, 0755); // Создаем директорию сохранения файла
        $file_perms = substr(sprintf('%o', fileperms($file_output_directory)), -4); // Запрашиваем права доступа к файлу
        if (is_writable($file_output_directory)) { // Если запись в директорию сохранения файла доступна
            $log_text .= "Директория создана. Запись в директорию " . $file_output_directory . " доступна. Права доступа " . $file_perms . ".\n"; // Выводим     уведомление, что директория создана и запись в нее доступна
        } else {
            $log_text .= "Директория не создана. Запись в директорию " . $file_output_directory . " недоступна. Права доступа " . $file_perms . ".\n"; //     Выводим уведомление, что директория не создана и запись в нее недоступна
        }
        if ($log_echo) {
            echo $log_text; // Выводим логи
        }
    }
}
outlets_sber();
?>