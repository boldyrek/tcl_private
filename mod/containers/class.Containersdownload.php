<?php

class ContainersDownload extends Proto {

   protected $_phpExcel;
   protected $_writer;

   const SENDER_NAME = 'SOBEX Enterprises Inc.';
   const SENDER_ADDRESS = '92 Dovetail Drive Richmond Hill ON L4E5A7';

   public function  __construct()
   {
      set_include_path(get_include_path().PATH_SEPARATOR.$_SERVER['DOCUMENT_ROOT'].'/lib/phpexcel/');

      include_once 'PHPExcel.php';

      $phpExcel = new PHPExcel;

      $this->_phpExcel = $phpExcel;

      $this->_writer = new PHPExcel_Writer_Excel5($phpExcel);
      
      parent::__construct();
   }

   public function  __destruct()
   {
      $this->_phpExcel->disconnectWorksheets();
      
      unset($this->_writer, $this->_phpExcel);
   }

   public function drawContent()
   {
      if ($this->checkAuth())
      {
         $action = $_GET['doc'];
         $id = (int) $_GET['id'];
         $this->$action($id);
      }

      $this->errorsPublisher();
      $this->publish();
   }

   public function __call($method, $arguments)
   {
      if (! method_exists($this, $method))
         throw new Exception('Page not found', 404);
   }

   // инвойс на авто
   private function invoice1($id)
   {
      include_once $_SERVER['DOCUMENT_ROOT'].'/lib/currency/semantic.php';
      include_once $_SERVER['DOCUMENT_ROOT'].'/lib/currency/semantic/ru.php';
      include_once $_SERVER['DOCUMENT_ROOT'].'/lib/currency/semantic/en.php';

      // set sheets font
      $font = new PHPExcel_Style_Font;
      $font->setName('Tahoma')
         ->setSize(10);

      // set sheets style
      $style = new PHPExcel_Style;
      $style->setFont($font);

      $boldStyle = array(
         'font' => array(
            'bold' => true
          )
      );
      
      $boldUnderlineStyle = array(
         'font' => array(
            'bold' => true,
            'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE
         )
      );

      $query = $this->mysqlQuery("SELECT * FROM `ccl_containers` WHERE `id` = $id");

      $container = mysql_fetch_object($query);

      unset($query);

      for ($i=1; $i<=5; $i++)
      {
         $slotId = (int) $container->{'slot'.$i};

         if ($slotId !== 0)
         {
            $sql = "SELECT
               c.frame AS `vincode`, c.model, c.engine, c.year, c.invoice_price, c.invoice_number,
               r.name, r.address
            FROM `ccl_cars` AS c
            LEFT JOIN `ccl_recievers` AS r ON c.reciever = r.id
            WHERE c.id = $slotId";

            $query = $this->mysqlQuery($sql);

            $car = mysql_fetch_object($query);

            // создаем лист и переключаемся на него
            $this->_phpExcel->createSheet();
            $this->_phpExcel->setActiveSheetIndex($i-1);

            $sheet = $this->_phpExcel->getActiveSheet();

            // задаем стиль шрифта и название листа
            $sheet->setDefaultStyle($style)
               ->setTitle('VIN '.substr($car->vincode, -6));

            // задаем ширину столбцов
            $sheet->getColumnDimension('A')->setWidth(4);
            $sheet->getColumnDimension('B')->setWidth(18);
            $sheet->getColumnDimension('C')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(13);
            $sheet->getColumnDimension('G')->setWidth(20);

            // задаем заголовок и стиль листа
            $sheet->setCellValue('C1', 'INVOICE & SPECIFICATIONS');
            $sheet->getStyle('C1')->applyFromArray($boldStyle);

            // если в форме машины задан номер инвойса
            if ($car->invoice_number != '')
            {
               $sheet->setCellValue('C2', 'Invoice #'.$car->invoice_number);
            }

            $sheet->setCellValue('A3', 'Sender / Отправитель');
            $sheet->getStyle('A3')->applyFromArray($boldUnderlineStyle);

            $sheet->setCellValue('A5', self::SENDER_NAME);
            $sheet->setCellValue('A6', self::SENDER_ADDRESS);
            $sheet->getStyle('A3:A6')->applyFromArray($boldStyle);

            $sheet->setCellValue('A13', 'Shipment cost included (CPT) / Стоимость транспортировки включена в стоимость товара');
            $sheet->setCellValue('A14', '(Условие поставки СРТ)');
            $sheet->getStyle('A13:A14')->applyFromArray($boldStyle);

            // задаем границы для таблицы
            $sheet->getStyle('A16:G18')
               ->applyFromArray(array(
                  'borders' => array(
                     'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                     )
                  )
               ));

            // задаем стили для заголовка таблицы
            // по центру с переносом строки
            $sheet->getStyle('A16:G16')
               ->getAlignment()
               ->setWrapText(true)
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            // жирный шрифт размером в 8px
            $sheet->getStyle('A16:G16')
               ->applyFromArray(array(
                  'font' => array(
                     'bold' => true,
                     'size' => 8
                  )
               ));

            // заполняем заголовок таблицу
            $sheet
               ->setCellValue('A16', 'No.')
               ->setCellValue('B16', 'Buyer / Покупатель')
               ->setCellValue('C16', 'Mark&Model / Марка и модель')
               ->setCellValue('D16', 'Engine volume / Объем двиг.')
               ->setCellValue('E16', 'Price / Сумма (USD)')
               ->setCellValue('F16', 'Production year / Год вып.')
               ->setCellValue('G16', 'VIN code / Идентификационный номер');

            // задаем стили для тела таблицы
            // автовысота для всей строки
            $sheet->getRowDimension(16)
               ->setRowHeight(-1);

            // перенос строки
            $sheet->getStyle('A17:G17')
               ->getAlignment()
               ->setWrapText(true);

            $sheet->getStyle('B17')
               ->getAlignment()
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            // кроме покупателя вся строка по центру
            $sheet->getStyle('A17')
               ->getAlignment()
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            
            $sheet->getStyle('C17:G17')
               ->getAlignment()
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            // заполняем тело таблицы
            $sheet
               ->setCellValue('A17', 1)
               ->setCellValue('B17', trim($car->name."\n".$car->address))
               ->setCellValue('C17', $car->model)
               ->setCellValue('D17', $car->engine)
               ->setCellValue('E17', $car->invoice_price)
               ->setCellValue('F17', $car->year)
               ->setCellValue('G17', $car->vincode);

            // задаем стили для итогов таблицы
            // все по центру
            $sheet->getStyle('C18:E18')
               ->getAlignment()
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            // жирный шрифт
            $sheet->getStyle('C18:E18')->applyFromArray($boldStyle);

            // заполняем итоги таблицы
            $sheet
               ->setCellValue('C18', 'TOTAL / ИТОГО:')
               ->setCellValue('E18', $car->invoice_price);

            // задаем стили для суммы прописью
            // горизонтально спарва
            $sheet->getStyle('C20:C21')
               ->getAlignment()
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            // жирный шрифт
            $sheet->getStyle('C20:C21')->applyFromArray($boldStyle);

            // заполняем суммы прописью
            $sheet
               ->setCellValue('C20', 'Total price:')
               ->setCellValue('D20', Currency_Semantic::factory($car->invoice_price, 'en')->toString())
               ->setCellValue('C21', 'Итого сумма:')
               ->setCellValue('D21', Currency_Semantic::factory($car->invoice_price, 'ru')->toString());

            // задаем стили для даты
            $sheet->getStyle('F23')
               ->getAlignment()
               ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            // жирный
            $sheet->getStyle('F23')->applyFromArray($boldStyle);

            // заполняем дату
            $sheet
               ->setCellValue('F23', 'Date / Дата:')
               ->setCellValue('G23', date('d.m.Y', strtotime($container->sent)));
            
            // если контейнер собственный, то задаем номер контейнера и инф. о получателе
            if ($container->own)
            {
               $sheet->setCellValue('E2', 'Cont. #'.$container->number);

               $sheet->setCellValue('A8', 'Receiver / Получатель');
               $sheet->getStyle('A8')->applyFromArray($boldUnderlineStyle);

               $sheet->setCellValue('A10', $container->reciever_name);
               $sheet->setCellValue('A11', 'address: '.$container->reciever_address);
               $sheet->getStyle('A10:A11')->applyFromArray($boldStyle);
            }
            // иначе удаляем 5 строк начиная от 8-строки
            // NOTICE: удалять надо в самом конце после завершения формирования листа
            else
            {
               $sheet->removeRow(8, 5);
            }
         }
      }

      // отправляем на скачивание
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="invoice-cars-'.time().'.xls"');
      header('Cache-Control: max-age=0');

      $this->_writer->save('php://output');

      exit;
   }

   // инвойс на контейнер
   private function invoice2($id)
   {
      $query = $this->mysqlQuery("SELECT * FROM `ccl_containers` WHERE `id` = $id");

      $container = mysql_fetch_object($query);

      // set sheets font
      $font = new PHPExcel_Style_Font;
      $font->setName('Arial')
         ->setSize(9);

      // set sheets style
      $style = new PHPExcel_Style;
      $style->setFont($font);

      $boldStyle = array('font' => array('bold' => true));
      $borderStyle = array(
         'borders' => array(
            'allborders' => array(
               'style' => PHPExcel_Style_Border::BORDER_THIN
            )
         )
      );

      $this->_phpExcel->setActiveSheetIndex();

      $sheet = $this->_phpExcel->getActiveSheet();

      $sheet->setDefaultStyle($style);

      $sheet->getColumnDimension('A')->setWidth(18);
      // $sheet->getColumnDimension('G')->setWidth(7);
      // $sheet->getColumnDimension('H')->setWidth(7);
      // $sheet->getColumnDimension('I')->setWidth(9);

      $sheet->getRowDimension(1)->setRowHeight(30);
      $sheet->getRowDimension(2)->setRowHeight(30);
      $sheet->getRowDimension(3)->setRowHeight(30);
      $sheet->getRowDimension(21)->setRowHeight(30);
      $sheet->getRowDimension(44)->setRowHeight(30);

      $sheet->setCellValue('B1', self::SENDER_NAME);
      $sheet->setCellValue('B2', self::SENDER_ADDRESS);
      $sheet->mergeCells('B1:F1');
      $sheet->mergeCells('B2:F2');
      $sheet->getStyle('B1:B3')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $sheet->setCellValue('H1', 'INVOICE');
      $sheet->setCellValue('H2', 'Date');
      $sheet->setCellValue('I2', 'Invoice #');
      $sheet->setCellValue('H3', date('d-M-y', strtotime($container->sent)));
      $sheet->mergeCells('H1:I1');
      $sheet->getStyle('H1:I3')->applyFromArray($borderStyle);
      $sheet->getStyle('H1')->applyFromArray($boldStyle);
      $sheet->getStyle('H1:I3')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $sheet->setCellValue('A6', 'Bill to');
      $sheet->setCellValue('E6', 'P.O. No.');
      $sheet->setCellValue('A7', $container->reciever_name."\naddress:".$container->reciever_address);
      $sheet->mergeCells('A7:D11');
      $sheet->mergeCells('A6:D6');
      $sheet->mergeCells('E6:I6');
      $sheet->mergeCells('E7:I8');
      $sheet->getStyle('A6:I6')->applyFromArray($borderStyle);
      $sheet->getStyle('A7:D11')->applyFromArray($borderStyle);
      $sheet->getStyle('E7:I8')->applyFromArray($borderStyle);
      $sheet->getStyle('A7')->applyFromArray($boldStyle);
      $sheet->getStyle('A6:I11')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $sheet->setCellValue('A12', 'AWB/B/L No.');
      $sheet->setCellValue('A13', 'HAWB No.');
      $sheet->setCellValue('A14', 'CARRIER FTL/VESS');
      $sheet->setCellValue('A15', 'CONTAINER #');
      $sheet->setCellValue('A16', 'ETD');
      $sheet->setCellValue('A17', 'DRO');
      $sheet->setCellValue('A18', 'ORIGIN');
      $sheet->setCellValue('A19', 'PCS');
      $sheet->setCellValue('B15', $container->number);
      $sheet->setCellValue('B18', 'U.S.A');
      $sheet->mergeCells('B12:D12');
      $sheet->mergeCells('B13:D13');
      $sheet->mergeCells('B14:D14');
      $sheet->mergeCells('B15:D15');
      $sheet->getStyle('B12:D19')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $sheet->setCellValue('C16', 'ETA');
      $sheet->setCellValue('C17', 'DESTIN');
      $sheet->setCellValue('C18', 'KGS');
      $sheet->setCellValue('C19', 'CBM/W');
      $sheet->setCellValue('D16', 'ETA');
      $sheet->setCellValue('D17', 'BISHKEK');
      $sheet->getStyle('A12:D19')->applyFromArray($borderStyle);
      $sheet->getStyle('A12:A19')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $sheet->setCellValue('G17', 'Terms');
      $sheet->setCellValue('H17', 'Project');
      $sheet->mergeCells('G18:G19');
      $sheet->mergeCells('H17:I17');
      $sheet->mergeCells('H18:I19');
      $sheet->getStyle('G17:I19')->applyFromArray($borderStyle);
      $sheet->getStyle('G17:I19')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      $sheet->setCellValue('A21', 'Cargo Description');
      $sheet->setCellValue('A22', 'Container #'.$container->number);
      $sheet->setCellValue('B21', 'Charges Description');
      $sheet->setCellValue('B22', 'SHIPPER OWNED CONTAINER PURCHASE-40\'HQ');
      $sheet->setCellValue('F21', 'Quantity');
      $sheet->setCellValue('G21', 'Rate');
      $sheet->setCellValue('G22', 350);
      $sheet->setCellValue('H21', 'Amount');
      $sheet->setCellValue('H22', 350);
      $sheet->setCellValue('A42', 'Container\'s origin USA');
      $sheet->setCellValue('G42', 'TOTAL');
      $sheet->setCellValue('H42', '$350');
      $sheet->mergeCells('A22:A41');
      $sheet->mergeCells('B21:E21');
      $sheet->mergeCells('B22:E41');
      $sheet->mergeCells('F22:F41');
      $sheet->mergeCells('G22:G41');
      $sheet->mergeCells('H21:I21');
      $sheet->mergeCells('H22:I41');
      $sheet->mergeCells('A42:F42');
      $sheet->mergeCells('H42:I42');
      $sheet->getStyle('A21:I42')->applyFromArray($borderStyle);
      $sheet->getStyle('A22')->applyFromArray($boldStyle);
      $sheet->getStyle('A42')->applyFromArray($boldStyle);
      $sheet->getStyle('G42:I42')->applyFromArray($boldStyle);
      $sheet->getStyle('A21:I21')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $sheet->getStyle('A22:I22')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
      $sheet->getStyle('G42:I42')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

      $sheet->setCellValue('H44', 'Original Stamp here');
      $sheet->mergeCells('H44:I44');
      $sheet->getStyle('H44')->applyFromArray($boldStyle);
      $sheet->getStyle('H44:I44')
         ->getAlignment()
         ->setWrapText(true)
         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="invoice-container-'.time().'.xls"');
      header('Cache-Control: max-age=0');

      $this->_writer->save('php://output');

      exit;
   }

   private function customer($id)
   {
      $query = $this->mysqlQuery("SELECT * FROM `ccl_containers` WHERE `id` = $id");

      $container = mysql_fetch_object($query);

      unset($query);

      $replace = array();

      for ($i=1; $i<=5; $i++)
      {
         $slotId = (int) $container->{'slot'.$i};

         if ($slotId !== 0)
         {
            $sql = "SELECT
               c.frame AS `vincode`, c.year, c.invoice_price AS `price`,
               ma.name AS `make`,
               mo.name AS `model`,
               r.name, r.address
            FROM `ccl_cars` AS c
            LEFT JOIN `ccl_recievers` AS r ON (c.reciever = r.id)
            LEFT JOIN `ccl_marka` AS ma ON (c.car_marka = ma.id)
            LEFT JOIN `ccl_model` AS mo ON (c.car_model = mo.id)
            WHERE c.id = $slotId";

            $query = $this->mysqlQuery($sql);

            $car = mysql_fetch_object($query);

            $html = '
               <table width="100%" border="1" cellspacing="0" cellpadding="3">
                  <tbody>
                     <tr><td><b>YEAR:</b> '.$car->year.'</td></tr>
                     <tr><td><b>MAKE:</b> '.$car->make.'</td></tr>
                     <tr><td><b>MODEL:</b> '.$car->model.'</td></tr>
                     <tr><td><b>VIN:</b> '.$car->vincode.'</td></tr>
                     <tr><td><b>VALUE:</b> '.$car->price.'</td></tr>
                     <tr><td><b>CONSIGNEE (If different from above):</b><br />'.$car->name.'<br />Address: '.$car->address.'</td></tr>
                  </tbody>
               </table>
            ';

            $replace['{SLOT'.$i.'}'] = $html;
         }
         else
         {
            $replace['{SLOT'.$i.'}'] = '';
         }
      }

      $template = file_get_contents(dirname(__FILE__).'/templates/customer.html');

      header('Content-Type: application/msword');
      header('Content-Disposition: attachment;filename="customer-form-'.time().'.doc"');
      header('Cache-Control: max-age=0');

      echo strtr($template, $replace);
      
      exit;
   }

   private function shipping($id)
   {
      $query = $this->mysqlQuery("SELECT * FROM `ccl_containers` WHERE `id` = $id");

      $container = mysql_fetch_object($query);
      
      unset($query);

      $replace = array(
         '{NUMBER}' => $container->number,
         '{RECIEVER_NAME}' => $container->reciever_name,
         '{RECIEVER_ADDRESS}' => $container->reciever_address
      );

      for ($i=1; $i<=5; $i++)
      {
         $slotId = (int) $container->{'slot'.$i};

         if ($slotId !== 0)
         {
            $sql = "SELECT
               c.frame AS `vincode`, c.year, c.invoice_price AS `price`,
               ma.name AS `make`,
               mo.name AS `model`,
               r.name, r.address, r.phone
            FROM `ccl_cars` AS c
            LEFT JOIN `ccl_recievers` AS r ON (c.reciever = r.id)
            LEFT JOIN `ccl_marka` AS ma ON (c.car_marka = ma.id)
            LEFT JOIN `ccl_model` AS mo ON (c.car_model = mo.id)
            WHERE c.id = $slotId";

            $query = $this->mysqlQuery($sql);

            $car = mysql_fetch_object($query);

            $html = '
               <table width="100%" cellspacing="0" cellpadding="3">
                  <tbody>
                     <tr><td><b><u>Vehicle '.$i.'</u></b></td></tr>
                     <tr><td>Year: '.strtoupper($car->year).'</td></tr>
                     <tr><td>Make: '.strtoupper($car->make).'</td></tr>
                     <tr><td>Model: '.strtoupper($car->model).'</td></tr>
                     <tr><td>VIN: '.strtoupper($car->vincode).'</td></tr>
                     <tr><td>Price: '.$car->price.'</td></tr>
                     <tr><td><b>Consignee</b><br />Name: '.strtoupper($car->name).'<br />Address: '.strtoupper($car->address).'<br />Telephone: '.$car->phone.'</td></tr>
                  </tbody>
               </table>
            ';

            $replace['{SLOT'.$i.'}'] = $html;
         }
         else
         {
            $replace['{SLOT'.$i.'}'] = '';
         }
      }
      
      $template = file_get_contents(dirname(__FILE__).'/templates/shipping.html');

      header('Content-Type: application/msword');
      header('Content-Disposition: attachment;filename="shipping-form-'.time().'.doc"');
      header('Cache-Control: max-age=0');

      echo strtr($template, $replace);

      exit;
   }

}