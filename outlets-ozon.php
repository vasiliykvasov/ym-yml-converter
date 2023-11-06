<?
outletsOzon();
function outletsOzon() { // Изначальная функция countToOutlets для Ozona
    // Запись логов
    $log_file = "ozon-logs.txt";
    $log_text = "История действий " . date('j.m.Y H:i:s') . "\n\n";
    $log_echo = false;
    // // Входные данные
   
   $file_list = array (
        array (
            "array_file_feed" => "https://mptls.vasiliykvasov.ru/ym-yml-converter/example.xml",
            "array_file_mod" => "test",
            "array_option_prices" => true,
            "array_option_outlets" => true,
            "array_price_premium" => 0.05,
            "array_warehouse_id" => "test",
            "array_min_number_sales" => 2
        )
    );
    
    // // Данные для сохранения
    $file_output_directory = getcwd() . "/outlets-ozon/"; // Директория сохранения файла
    // Проверяем, можно ли сохранить файл в указанную директорию
    $file_perms = substr(sprintf('%o', fileperms($file_output_directory)), -4); // Запрашиваем права доступа к файлу
    if (is_writable($file_output_directory)) { // Если запись в директорию сохранения файла доступна
        $log_text .= "Запись в директорию " . $file_output_directory . " доступна. Права доступа " . $file_perms . ".\n\n"; // Выводим уведомление, что запись в     директорию доступна
    // Действия с элементами
        foreach ($file_list as $file) { // С каждым файлом в списке файлов делаем следующее 
                      
                $generate_file_feed = $file["array_file_feed"]; // входный файл xml
                $generate_file_mod = $file["array_file_mod"]; // префикс для получаемого файла
                $generate_option_prices = $file["array_option_prices"];
                $generate_option_outlets = $file["array_option_outlets"];
                $generate_price_premium = $file["array_price_premium"];
                $generate_warehouse_id = $file["array_warehouse_id"];
                $generate_min_number_sales = $file["array_min_number_sales"];
                
                if(empty($generate_warehouse_id)) { //если нет названания склада, то
                    $log_text .= "У файла " . $generate_file_feed . ": нет названия склада \n"; // Выводим     уведомление, о наличии складах
                } else { // иначе

                if (fopen($generate_file_feed, "r")) { // Проверяем существование удаленного файла
                        $log_text .= "Файл " . $generate_file_feed . " получен.\n\n"; // Выводим уведомление, что файл получен
                        $xml = simplexml_load_file($generate_file_feed); // Получаем XMl файл
                        $root = <<<XML
                        <?xml version="1.0" encoding="utf-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd"><yml_catalog><shop><offers></offers></shop></yml_catalog> 
                        XML;
                        $xmlRoot = new SimpleXMLElement($root); // cоздаем новый YML файл для Ozona

                        // Добавляем дату в формате RFC 3339
                        $date_moscow = new DateTime('now', new DateTimeZone('Europe/Moscow'));
                        $date_moscow = $date_moscow->format(DATE_RFC3339);
                        $xmlRoot->addAttribute('date', $date_moscow);

                        foreach ($xml->shop->offers->offer as $ride_offer) { // Каждый offer записываем в переменную read_offer
                            $read_id = $ride_offer['id']; // Записываем ID
                            $read_name = $ride_offer->name; // Записываем наименование товара
                            $read_price = $ride_offer->price; //получаем цену товара
                            $read_oldprice = $ride_offer->oldprice; //получаем цену товара
                            $read_count = $ride_offer->count; //получаем остаток товара +s
                            $read_vendore_code = $ride_offer->vendorCode; // получаем артикул товара +
                            $write_offers = $xmlRoot->shop->offers; // получаем доступ к тегу offers в новом файле
                            if (empty($read_vendore_code)) {  // проверяем если артикула товара нет
                                $log_text .= "[" . $read_id . "] " . $read_name . " нет артикула товара.\n"; // Выводим уведомления, что у элемента нет артикула
                            } else { // если артикул товара есть
                                $write_offer = $write_offers->addChild('offer'); // тегу offers добавляем дочерний тег offer
                                $write_offer->addAttribute('id', $read_vendore_code); // тегу offer добавляем атрибут с названием 'id' и со значением артикула товара
                                $write_offer->addChild('name', $read_name);
                                $log_text .= "\n[" . $read_id . "] " . $read_name . ": " . $read_vendore_code . " - артикул товара получен корректно.\n"; // Выводим     уведомление, о наличии артикула
                                if ($generate_option_prices === true) {
                                    if (empty($read_price)) { // проверяем если цены нет
                                        $log_text .= "[" . $read_id . "] " . $read_name . " нет цены у товара.\n"; // Выводим уведомления, что у элемента нет цены
                                    } else { 
                                        $write_offer->addChild('price', $read_price); // добавляем тег price с розничной ценой со скидкой
                                        if (!empty($read_oldprice)) { // проверяем наличие oldprice
                                            $log_text .= "[" . $read_id . "] " . $read_name . "  имеется тег <oldprice> со значением:" .$read_oldprice . "\n"; // Выводим наличие тэга oldprice в лог
                                            if ($read_price > 10000 && ($read_oldprice - $read_price) < 500 ) { // если  больше 10 000, то разница между price и oldprice должна быть больше 500 руб
                                                $log_text .= "[" . $read_id . "] " . $read_name . " цена больше 10000, и разница price: " . $read_price . " и oldprice: " . $read_oldprice . "меньше 500 рублей \n"; // что цена больше 10000, и разница price и oldprice меньше 500 рублей.
                                            } elseif ($read_price <= 10000 && ($read_price/$read_oldprice) <= 0.95 ) { // если стоит меньше 10 000, то разница между price и oldprice должна быть больше 5%
                                                $log_text .= "[" . $read_id . "] " . $read_name . " цена меньше 10000, и разница price: " . $read_price . " и oldprice: " . $read_oldprice . "меньше 5% рублей \n"; // цена меньше 10000, и разница price и oldprice меньше 5% рублей.
                                            } else { 
                                                $write_offer->addChild('oldprice', $read_oldprice); // добавляем тег oldprice с розничной ценой без скидки
                                            }
                                            $price_ozon_premium = $read_price - ($read_price * $generate_price_premium); // устанавливаем 5% скидку для премиум аккаунтов на ozone
                                            $write_offer->addChild('premium_price', $price_ozon_premium); // добавляем цену для покупателей с подпиской Ozon Premium
                                            $log_text .= "[" . $read_id . "] " . $read_name . ": " . $read_price . " - цена товара получена корректно.\n"; // Выводим уведомление, о наличии цены
                                        } else {  // если нет oldprice
                                            $log_text .= "[" . $read_id . "] " . $read_name . " нет тега <oldprice> \n";
                                        }

                                    }
                                }
                                $write_outlets = $write_offer->addChild('outlets'); // Создаем тег <outlets>
                                $write_outlet = $write_outlets->addChild('outlet', ' '); // Создаем тег <outlet>
                                $write_outlet->addAttribute('warehouse_name', $generate_warehouse_id); // тегу <outlet> добавляем атрибут 'warehouse_name' со значением идентификатора склада
                                if ($generate_option_outlets === true) {
                                    if (empty($read_count)) { // проверяем если остатков нет
                                        $log_text .= "[" . $read_id . "] " . $read_name . " нет в остатках.\n\n"; // Выводим уведомления, что у элемента нет остатков
                                    } else { // если остатки есть
                                        $write_outlet->addAttribute('instock', $read_count); // Добавляем тегу <outlet> атрибут instock со значением количества остатков
                                    if ($read_count >= $generate_min_number_sales) {
                                        $log_text .= "[" . $read_id . "] " . $read_name . ": " . $read_count . " на складе " . $generate_warehouse_id . ".\n\n"; // Выводим     уведомление,     об остатках
                                        } else {
                                            $log_text .= "[" . $read_id . "] " . $read_name . ": недостаточное количество остатков для продажи на складе " . $generate_warehouse_id . ".\n\n"; // Выводим     уведомление,     об остатках
                                        }
                                    }
                                } 
                            }
                        };
                        header('content-type: text/xml');
                        // header('Content-Disposition: attachment; filename=' . basename($generate_file_feed));
                        echo($xmlRoot->asXML());
                        $file_output = $file_output_directory . $generate_file_mod ."-" . basename($generate_file_feed); // Создаем новое имя файла
                        if (is_writable($file_output_directory)) { // Если запись в директорию сохранения файла доступна
                            $xmlRoot->asXML($file_output); // Сохраняем XML файл
                            $log_text .= "Файл " . $file_output . " записан." . "\n\n"; // Выводим уведомление, что файл записан
                        } else { // Если запись в директорию сохранения файла недоступна
                            $log_text .= "Файл " . $file_output . " не записан." . "\n\n";
                        }
                    } else { // Если удаленного файла нет
                        $log_text .= 'Не удалось открыть файл ' . $generate_file_feed . ".\n\n"; // Выводим уведомление, что не удалось открыть файл
                    } 
                }       
        }; // конец foreach file_list
        file_put_contents ($file_output_directory . $log_file, $log_text); // Записываем логи в файл
        if ($log_echo) {
			echo "Логи записаны в " . $file_output_directory . $log_file . ".\n"; // Выводим уведомление об успешной записи
        } 
        } else { // Если запись в директорию сохранения файла не доступна
        $log_text .= "Запись в директорию " . $file_output_directory . " недоступна. Права доступа " . $file_perms . ".\n"; // Выводим уведомление, что запись     в директорию файла недоступна
        mkdir($file_output_directory, 0755); // Создаем директорию сохранения файла
        $file_perms = substr(sprintf('%o', fileperms($file_output_directory)), -4); // Запрашиваем права доступа к файлу
        if (is_writable($file_output_directory)) { // Если запись в директорию сохранения файла доступна
            $log_text .= "Директория создана. Запись в директорию " . $file_output_directory . " доступна. Права доступа " . $file_perms . ".\n"; // Выводим уведомление, что директория создана и запись в нее доступна
        } else {
            $log_text .= "Директория не создана. Запись в директорию " . $file_output_directory . " недоступна. Права доступа " . $file_perms . ".\n"; //     Выводим уведомление, что директория не создана и запись в нее недоступна
        }
        if ($log_echo) {
            echo $log_text; // Выводим логи
        }
     } 
}
?>