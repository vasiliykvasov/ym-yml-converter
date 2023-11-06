<?
function outlets_sber() { // Изначальная функция countToOutlets для Сбера
    // Входные данные
    $file_list = array( // Массив с файлами
        "https://mptls.vasiliykvasov.ru/ym-yml-converter/test.xml"
    );
    $warehouse_id = "1"; // Идентификатор склада
        foreach ($file_list as $file) { // С каждым файлом в списке файлов делаем следующее
            if (fopen($file, "r")) { // Проверяем существование удаленного файла
                $xml = simplexml_load_file($file); // Получаем XML
                $xml->xmlEndoding='UTF-8';
                foreach ($xml->shop->offers->offer as $element_offer) { // Каждый offer записываем в переменную element_offer
                    $element_count = $element_offer->count; // Записываем количество остатков
                    $element_id = $element_offer["id"]; // Записываем ID
                    $element_name = $element_offer->name; // Записываем наименование
                    if (empty($element_count)) { // Если остатков нет
                    } else { // Если остатки есть
                        $element_outlets = $element_offer->addChild('outlets'); // Создаем элемент <outlets>
                        $element_outlet = $element_outlets->addChild('outlet'); // Создаем элемент <outlet>
                        $element_outlet->addAttribute('id', $warehouse_id); // Добавляем элементу <outlets> свойство id со значением идентификатора склада
                        $element_outlet->addAttribute('instock', $element_count); // Добавляем элементу <outlets> свойство instock со значением количества остатков
                    }
                    $file = $xml->asXML();
                    // header('content-type: text/xml');
                    echo($file);
                    // header("Content-type: text/xml");
                    // header('Content-Disposition: attachment; filename="sample.xml"');
                    // header('Content-Type: text/xml'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
                    // header('Content-Length: ' . strlen($file));
                    // header('Connection: close');
                    // echo($file);

                    /* header('Content-disposition: attachment; filename=advertise.xml');
                    header ("Content-Type:text/xml"); 
                    echo($xml->asXML());
                    header("Expires: 0") */

                    // html_entity_decode($xml, ENT_NOQUOTES, 'UTF-8');
                    /* $xml->asXML($file); // Выводим XML файл
                        header('Content-Description: File Transfer');
                        header('Content-Type: text/xml');
                        header('Content-Disposition: attachment; filename='.basename($file));
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($file));
                        readfile($file);
                        exit; */
                }
            }
        }
}
outlets_sber();
?>