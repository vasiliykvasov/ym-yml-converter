<?
function convert_yml(
    $input_feed_type, // "ozon" - фид для Ozon, "sber" - фид для Мегамаркета
    $input_option_save, // "view" — открыть на странице, "download" — скачать, "/path/to/folder" — путь к существующей доступной для записи папке на сервере
    $input_file_feed, // Ссылка на локальный или удаленный фид Яндекс Маркета
    $input_option_prices, // Обновлять цены: "on" или "off"
    $input_option_outlets, // Обновлять остатки: "on" или "off"
    $input_warehouse_id, // Идентификатор склада
    $input_savename // Название файла при сохранении
    ) {
    if (fopen($input_file_feed, "r")) { // Если фид существует
        $input_xml = simplexml_load_file($input_file_feed); // Получаем XMl файл
        $root = <<<XML
        <?xml version="1.0" encoding="utf-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd"><yml_catalog><shop><offers></offers></shop></yml_catalog> 
        XML;
        $output_xml = new SimpleXMLElement($root); // Создаем новый YML файл
        $date_moscow = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $date_moscow = $date_moscow->format(DATE_RFC3339);
        $output_xml->addAttribute('date', $date_moscow); // Добавляем дату в формате RFC 3339
        foreach ($input_xml->shop->offers->offer as $read_offer) { // Каждый offer записываем в переменную read_offer
            $read_id = $read_offer['id']; // Записываем ID
            $read_name = $read_offer->name; // Записываем наименование товара
            if ($input_option_prices) { // Если нужно обновлять цены
                $read_price = $read_offer->price; // Получаем цену товара
                $read_oldprice = $read_offer->oldprice; // Получаем старую цену товара
            }
            if ($input_option_outlets) { // Если нужно обновлять остатки
                $read_count = $read_offer->count; // Получаем остаток товара
            }
            $write_offers = $output_xml->shop->offers; // Получаем доступ к тегу offers в новом файле
            $write_offer = $write_offers->addChild('offer'); // Добавляем тегу offers  дочерний тег offer
            $write_offer->addAttribute('id', $read_id); // Добавляем тегу offer атрибут 'id' со значением артикула товара
            $write_offer->addChild('name', $read_name); // Добавляем тэгу offer атрибут 'name' со значением названия товара
            if ($input_option_prices) { // Если нужно обновлять цены
                $write_offer->addChild('price', $read_price); // Добавляем тег price с розничной ценой со скидкой
                if (!empty($read_oldprice)) { // Проверяем наличие oldprice
                    if ($read_price > 10000 && ($read_oldprice - $read_price) < 500 ) { // Если цена больше 10 000, то разница между price и oldprice должна быть больше 500 руб
                    } elseif ($read_price <= 10000 && ($read_price/$read_oldprice) <= 0.95 ) { // Если стоит меньше 10 000, то разница между price и oldprice должна быть больше 5%
                    } else { 
                        $write_offer->addChild('oldprice', $read_oldprice); // Добавляем тег oldprice с розничной ценой без скидки
                    }
                }
            }
            if ($input_option_outlets) { // Если нужно обновлять остатки
                $write_outlets = $write_offer->addChild('outlets'); // Создаем тег outlets
                $write_outlet = $write_outlets->addChild('outlet'); // Создаем тег outlet
                $write_outlet->addAttribute('instock', $read_count); // Добавляем тегу outlet атрибут instock со значением количества остатков
                if ($input_feed_type === "ozon") {
                    $write_outlet->addAttribute('warehouse_name', $input_warehouse_id); // тегу outlet добавляем атрибут 'warehouse_name' со значением идентификатора склада
                } elseif ($input_feed_type === "megamarket") {
                    $write_outlet->addAttribute('id', $input_warehouse_id); // тегу outlet добавляем атрибут 'id' со значением идентификатора склада
                }
            }
        }
        if ($input_option_save === "view" || $input_option_save === "download") { // Если скрипт выводит XML на экран или сохраняет на компьютер
            header('content-type: text/xml'); // Добавляем заголовок, что это XML
            if ($input_option_save === "download") { // Если скрипт сохраняет файл на компьютер
                header('Content-Disposition: attachment; filename=' . $input_savename); // Добавялем заголовок с названием файла
            }
            echo($output_xml->asXML()); // Выводим XML
        } else { // Иначе
            $file_output = $input_option_save . $input_savename; // Создаем путь файла
            $output_xml->asXML($file_output); // Записываем файл
        }
    }
    return;
}