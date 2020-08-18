<?php
include 'vendor/autoload.php';

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;

if(!empty($_FILES)){
    $target_Path = "files/";
    $target_Path = $target_Path.basename( $_FILES['file']['name'] );
    move_uploaded_file( $_FILES['file']['tmp_name'], $target_Path );

    $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
    $reader->open($target_Path);

//    $reader->open($filePath);

    $headers = [];
    $products = [];
    $categories = [];
    foreach ($reader->getSheetIterator() as $sheet) {
        $i = 1;
        foreach ($sheet->getRowIterator() as $row) {
            if($i == 1) {
                $headers = $row;
            } else {
                $j = 0;
                $temp = [];

                if($row[0] == '') continue;

                foreach ($row as $item){
                    $temp[$headers[$j]] = $item;

                    $j++;
                }
                $products[] = $temp;

                if($row[2] != ''){
                    if(!in_array($row[2], $categories)){
                        $categories[] = $row[2];
                    }
                }
            }

            $i++;
        }
    }

    $reader->close();

    if(isset($_POST['filename']) && $_POST['filename'] != '') {
        $storageFile = __DIR__ . '/'.$_POST['filename'].'.xml';
    } else {
        $storageFile = __DIR__ . '/feed.xml';
    }

    // Очистка файла для выгрузки.
    file_put_contents( $storageFile , '');

    $xml = new XMLWriter();

    $xml->openMemory();				// Выделение памяти для строкового вывода.
    $xml->setIndent( true );		// Включение режима записи с отступами.
    $xml->setIndentString( '  ' );	// Установка строки отступа.

    $xml->startDocument( '1.0', 'UTF-8' );

    // <yml_catalog>
    $xml->startElement( 'yml_catalog' );
    $xml->writeAttribute( 'date', date('Y-m-d H:i') );

    // <name />
    if(isset($_POST['name']) && $_POST['name'] != '') {
        $xml->writeElement( 'name', $_POST['name'] );
    } else {
        $xml->writeElement( 'name', 'Интернет-магазин' );
    }

    // <currencies>
    $xml->startElement( 'currencies' );
        // <currency>
        $xml->startElement( 'currency' );

        $xml->writeAttribute( 'id', 'UAH' );
        $xml->writeAttribute( 'rate', 1 );

        $xml->text( $data['name'] );

        $xml->endElement();
        // </currency>
    $xml->endElement();
    // </currencies>

    // <categories>
    $xml->startElement( 'categories' );
    foreach ( $categories as $id => $data ) {
        // <category>
        $xml->startElement( 'category' );

        // [id="{category_id}"]
        $xml->writeAttribute( 'id', $id  + 1 );

        $text = explode(':', $data);

        if(isset($text[1])) {
            $xml->writeAttribute( 'rz_id', $text[1] );
        }

        $xml->text( $text[0] );

        $xml->endElement();
        // </category>
    }
    $xml->endElement();
    // </catalog>

// <offers>
    $xml->startElement( 'offers' );
    foreach ( $products as $id => $data ) {
        // <offer>
        $xml->startElement( 'offer' );

        // [id="{product_id}"]
        $xml->writeAttribute( 'id', $data['ID'] );

        // [available="true"]
        $xml->writeAttribute( 'available', 'true' );

        // <categoryId />
        $xml->writeElement( 'categoryId', array_search($data['category_id'], $categories) + 1 );

        // <currencyId />
        $xml->writeElement( 'currencyId', 'UAH' );

        // <name />
        $xml->writeElement( 'name', $data['Название товара'] );

        // <price />
        $xml->writeElement( 'price', $data['Стоимость'] );

        if($data['Цена старая'] != ''){
            // <price_old />
            $xml->writeElement( 'price_old', $data['Цена старая'] );
        }

        if($data['Цена по промокоду'] != ''){
            // <price_old />
            $xml->writeElement( 'price_promo', $data['Цена по промокоду'] );
        }

        // <vendorCode />
//        $xml->writeElement( 'vendorCode', $data['sku'] );

        // <description /> ...
        $xml->startElement( 'description' );
        $xml->writeCdata( $data['Описание'] );
        $xml->endElement();
        // ... <description />


        // <param /> ...
        if($data['Цвет'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Цвет' );

            $xml->text( $data['Цвет'] );
            $xml->endElement();
        }

        if($data['Пол'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Пол' );

            $xml->text( $data['Пол'] );
            $xml->endElement();
        }

        if($data['Размер'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Размер' );

            $xml->text( $data['Размер'] );
            $xml->endElement();
        }

        if($data['Тип товара'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Тип товара' );

            $xml->text( $data['Тип товара'] );
            $xml->endElement();
        }

        if($data['Вид товара'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Вид товара' );

            $xml->text( $data['Вид товара'] );
            $xml->endElement();
        }

        if($data['Стиль'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Стиль' );

            $xml->text( $data['Стиль'] );
            $xml->endElement();
        }

        if($data['Размер коробки'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Размер коробки' );

            $xml->text( $data['Размер коробки'] );
            $xml->endElement();
        }

        if($data['Детский возраст'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Детский возраст' );

            $xml->text( $data['Детский возраст'] );
            $xml->endElement();
        }

        if($data['Материал'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Материал' );

            $xml->text( $data['Материал'] );
            $xml->endElement();
        }

        if($data['Рост'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Рост' );

            $xml->text( $data['Рост'] );
            $xml->endElement();
        }

        if($data['Состав'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Состав' );

            $xml->text( $data['Состав'] );
            $xml->endElement();
        }

        if($data['Сезон'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Сезон' );

            $xml->text( $data['Сезон'] );
            $xml->endElement();
        }

        if($data['Вес'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Вес' );

            $xml->text( $data['Вес'] );
            $xml->endElement();
        }

        if($data['Элементы питания'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Элементы питания' );

            $xml->text( $data['Элементы питания'] );
            $xml->endElement();
        }

        if($data['Наклон спинки'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Наклон спинки' );

            $xml->text( $data['Наклон спинки'] );
            $xml->endElement();
        }

        if($data['Размеры в разложенном состоянии'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Размеры в разложенном состоянии' );

            $xml->text( $data['Размеры в разложенном состоянии'] );
            $xml->endElement();
        }

        if($data['Размеры в сложенном состоянии'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Размеры в сложенном состоянии' );

            $xml->text( $data['Размеры в сложенном состоянии'] );
            $xml->endElement();
        }

        if($data['Материал верха'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Материал верха' );

            $xml->text( $data['Материал верха'] );
            $xml->endElement();
        }

        if($data['Материал подкладки'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Материал подкладки' );

            $xml->text( $data['Материал подкладки'] );
            $xml->endElement();
        }

        if($data['Комплектация'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Комплектация' );

            $xml->text( $data['Комплектация'] );
            $xml->endElement();
        }

        if($data['Дополнительные характеристики'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Дополнительные характеристики' );

            $xml->text( $data['Дополнительные характеристики'] );
            $xml->endElement();
        }

        if($data['Особенность'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Особенность' );

            $xml->text( $data['Особенность'] );
            $xml->endElement();
        }

        if($data['Регулировка по высоте'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Регулировка по высоте' );

            $xml->text( $data['Регулировка по высоте'] );
            $xml->endElement();
        }

        if($data['Максимальный вес ребенка'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Максимальный вес ребенка' );

            $xml->text( $data['Максимальный вес ребенка'] );
            $xml->endElement();
        }

        if($data['Цвет провода'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Цвет провода' );

            $xml->text( $data['Цвет провода'] );
            $xml->endElement();
        }

        if($data['Страна производитель'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Страна производитель' );

            $xml->text( $data['Страна производитель'] );
            $xml->endElement();
        }

        if($data['Страна регистрации бренда'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Страна регистрации бренда' );

            $xml->text( $data['Страна регистрации бренда'] );
            $xml->endElement();
        }

        if($data['Длина провода с лампами'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Длина провода с лампами' );

            $xml->text( $data['Длина провода с лампами'] );
            $xml->endElement();
        }

        if($data['Общая длина гирлянды'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Общая длина гирлянды' );

            $xml->text( $data['Общая длина гирлянды'] );
            $xml->endElement();
        }

        if($data['Цвет свечения'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Цвет свечения' );

            $xml->text( $data['Цвет свечения'] );
            $xml->endElement();
        }

        if($data['Количество источников света, шт'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Количество источников света, шт' );

            $xml->text( $data['Количество источников света, шт'] );
            $xml->endElement();
        }

        if($data['Доставка/Оплата'] != ''){
            $xml->startElement( 'param' );
            $xml->writeAttribute( 'name', 'Доставка/Оплата' );

            $xml->text( $data['Доставка/Оплата'] );
            $xml->endElement();
        }

        if($data['URL фото'] != ''){
            $images = explode(',', $data['URL фото']);

            foreach ($images as $image) {
                $xml->writeElement( 'picture', 'https://'.trim($image) );
            }
        }

        if($data['Бренд'] != ''){
            $xml->writeElement( 'vendor', $data['Бренд'] );
        }

        if($data['Количество'] != ''){
            $xml->writeElement( 'stock_quantity', $data['Количество'] );
        }

        $xml->endElement();
        // </offer>
    }

    $xml->endElement();
    // </offers>

    $xml->endElement();
    // </yml_catalog>

    $xml->endDocument();

    file_put_contents( $storageFile , $xml->outputMemory() );

}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Feed Creator</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if(!empty($_FILES)):?>
        <p style="text-align:center;">
            <?php
            if(isset($_POST['filename']) && $_POST['filename'] != '') {
                echo '<a href="/'.$_POST['filename'].'.xml" target="_blank">Фид</a>';
            } else {
                echo '<a href="/feed.xml" target="_blank">Фид</a>';
            }
            ?>
        </p>
    <?php else:?>
        <form action="" method="post" enctype='multipart/form-data'>
            <h2>Создание xml фида</h2>
            <input type="text" name="filename" placeholder="Имя XML">
            <input type="text" name="name" placeholder="Параметр">
            <div>
                <input type="file" name="file" required>
                <input type="submit" value="Отправить">
            </div>
        </form>
    <?php endif;?>
</body>
</html>
