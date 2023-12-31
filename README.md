# YM YML Converter

YM YML Converter — инструмент для конвертации yml-фидов в формате Яндекс Маркета для других маркетплейсов:

- Ozon
- Мегамаркет

Эти маркетплейсы, как и Яндекс Маркет, принимают фиды в формате YML, но с некоторыми отличиями. В частности, отличается передача остатков товаров на складах. 

Требования к фидам на маркетплейсах:

- Ozon — [Обновление данных о товаре через фид](https://seller-edu.ozon.ru/work-with-goods/zagruzka-tovarov/created-goods/fidi)
- Мегамаркет — [Правила заполнения фида](https://partner-wiki.megamarket.ru/pravila-zapolneniya-fida-dlya-tovarnoj-kategorii-fashion-393286.html)

Результат работы скрипта, YML-документ, предназначен только для обновления цен и остатков товаров на складах, и не предназначен для добавления товаров, заполнения их характеристик и других действий.

Скрипт написан на PHP, и может обрабатывать данные формы в файле web.php или работать на сервере с данными в файле server.php.

## Заполнение веб-формы

Веб-форма доступна для работы по адресу [mptls.vasiliykvasov.ru/ym-yml-converter/](https://mptls.vasiliykvasov.ru/ym-yml-converter/). Форма отправляет GET-запрос в web.php и позволяет сохранить xml-файл или открыть его для просмотра.

## Работа на сервере

При работе полностью на сервере нужно добавить данные в файл server.php.

$save_path — путь к директории на сервере от /. Директория должна существовать и быть доступна для записи.
$file_list — Массив с фидами и параметрами их обработки.

Для автозапуска скрипта можно добавить server.php в cron.

## Параметры

Для обработки фидов нужно использовать следующие параметры.

### Тип фида

'feed_type' — тип фида для конвертации: 'ozon' - Ozon, 'megamarket' - Мегамаркет.

### Ссылка на фид

'file_feed' — ссылка на удаленный или локальный файл. В веб-форму можно добавить только ссылку на удаленный файл.

### Обновлять цены

'option_prices' — обновлять цены: true — да, false - нет. 

### Обновлять остатки

'option_outlets' — Обновлять остатки: true — да, false - нет. В веб-форме при включении появляется поле для идентифиактора склада.

### Открыть фид

При установленном флажке в веб-форме фид открывается на странице web.php с GET-параметрами из формы. Если флажок не установлен, файл сохраняется.

### Идентификатор склада

'warehouse_id' — Идентификатор склада для обновления остатков. Если не используется при работе на сервере, нужно оставить ''.

### Название файла

'save_name' — Название файла при сохранении




