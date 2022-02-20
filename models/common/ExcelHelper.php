<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-11-17
 * Time: 下午12:42
 */

namespace app\models\common;

use Yii;

class ExcelHelper
{
    public static $zip_path = 'tmp/zip_tmp/';

    /**
     * 输出下载header头到浏览器
     * @param string $filename 文件名
     */
    public static function setHeader($filename)
    {
//        print(chr(0xEF).chr(0xBB).chr(0xBF));
        $filename = self::backCorrectString($filename);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary ");
//        echo "\xEF\xBB\xBF"; // UTF-8 BOM
    }

    public static function xlsBOF()
    {
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
        return;
    }

    public static function xlsEOF()
    {
        echo pack("ss", 0x0A, 0x00);
        return;
    }

    public static function xlsWriteNumber($row, $col, $value)
    {
        echo pack("sssss", 0x203, 14, $row, $col, 0x0);
        echo pack("d", $value);
        return;
    }

    public static function xlsWriteLabel($row, $col, $value)
    {
        $length = strlen($value);
        echo pack("ssssss", 0x204, 8 + $length, $row, $col, 0x0, $length);
        echo $value;
        return;
    }

    /**
     * 设置默认列宽度
     */
    public static function setDefaultWidth($defaultColWidth = 8)
    {
        $record = 0x0055;   // Record identifier
        $length = 0x0002;   // Number of bytes to follow

        $header = pack("vv", $record, $length);
        $data = pack("v", $defaultColWidth);
        echo $header . $data;
        return;
    }

    /**
     * 通过低版本导出excel
     *
     * @param array $arr 数据数组
     * @param array $map 映射数组
     * @param string $filename 导出文件名
     * @param int $col_width 列宽度
     * @param string $dir 如果不为null，表示导出到某个文件夹而非直接输出到浏览器用于zip打包
     * @return      void
     */
    public static function export2DArray($arr, $map, $filename, $col_width = 8, $dir = null)
    {
        if ($dir === null) {
            @ob_end_clean();
            self::setHeader($filename);
        } else {
            ob_start();
        }
        self::xlsBOF();
        self::setDefaultWidth($col_width);
        foreach ($map as $map_key => $map_val) {
            self::xlsWriteLabel(0, $map_key, mb_convert_encoding($map_val['title'], 'gbk', 'utf-8'));
//            self::xlsWriteLabel(0, $map_key, self::backCorrectString($map_val['title']));
        }

        foreach ($arr as $row_key => $row_val) {
            $row_key += 1;
            foreach ($row_val as $col_key => $col_val) {
                foreach ($map as $map_key => $map_val) {
                    $type_field = isset($map_val['type']) ? $map_val['type'] : '';
                    if ($map_val['field'] == $col_key) {
                        switch ($type_field) {
                            case 'number':
                            case 'money':
                                self::xlsWriteNumber($row_key, $map_key, $col_val);
                                break;

                            case 'string':
                            default:
                                self::xlsWriteLabel($row_key, $map_key, mb_convert_encoding($col_val, 'gbk', 'utf-8'));
//                                self::xlsWriteLabel(0, $map_key, self::backCorrectString($col_val));
                                break;
                        }
                    }

                }
            }
        }

        self::xlsEOF();

        if ($dir !== null) {
            return self::putObToFile($dir . $filename);
        }
    }

    /**
     * 输出xml头和样式
     */
    public static function xmlSetStyle()
    {
        echo <<<HEREDOC
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook
  xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="urn:schemas-microsoft-com:office:spreadsheet"
  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">

<Styles>
 <Style ss:ID="Default" ss:Name="Normal">
  <Alignment ss:Vertical="Bottom"/>
  <Borders/>
  <Font/>
  <Interior/>
  <NumberFormat/>
  <Protection/>
 </Style>
 <Style ss:ID="sbold">
  <Font x:Family="Swiss" ss:Color="#0000FF" ss:Bold="1"/>
 </Style>
 <Style ss:ID="s21">
  <NumberFormat ss:Format="yyyy\-mm\-dd"/>
 </Style>
 <Style ss:ID="s22">
  <NumberFormat ss:Format="yyyy\-mm\-dd\ hh:mm:ss"/>
 </Style>
 <Style ss:ID="s23">
  <NumberFormat ss:Format="hh:mm:ss"/>
 </Style>
</Styles>
HEREDOC;
    }

    /**
     * 用XML格式到处excel
     * @param array $arr 要导出的数据数组
     * @param array $map 表头数组
     * @param string $filename 文件名 UTF8编码
     * @param int $col_width 单元格长度，暂时无效。office长度，三种到处长度都不同
     * @param string $dir 输出目录，如果不为null，则不输出到网页而生成到本地
     * @return      void
     */
    public static function export2DArrayByXML($arr, $map, $filename, $col_width = 100, $dir = null)
    {
        @ob_end_clean();
        if (is_null($dir)) {
            self::setHeader($filename);
        } else {
            ob_start();
        }
        self::xmlSetStyle();
        echo '<Worksheet ss:Name="Sheet1">
                <ss:Table ss:DefaultColumnWidth="100">';

        // 输出表头
        echo '<ss:Row>';
        foreach ($map as $k => $v) {
            echo "<ss:Cell><Data ss:Type=\"String\">" . $v['title'] . "</Data></ss:Cell>";
        }
        echo '</ss:Row>';

        // 输出数据
        foreach ($arr as $row_key => $row_val) {
            echo '<ss:Row>';
            foreach ($map as $map_key => $map_val) {
                $col_val = isset($row_val[$map_val['field']]) ? $row_val[$map_val['field']] : '';
                $type_field = isset($map_val['type']) ? $map_val['type'] : '';
                switch ($type_field) {
                    case 'number':
                        echo "<ss:Cell><Data ss:Type=\"Number\">" . $col_val . "</Data></ss:Cell>";
                        break;
                    case 'money':
                        echo "<ss:Cell><Data ss:Type=\"String\">" . $col_val . "</Data></ss:Cell>";
                        break;
                    case 'date':
                    case 'string':
                    default:
                        echo "<ss:Cell><Data ss:Type=\"String\">" . $col_val . "</Data></ss:Cell>";
                        break;
                }
            }
            echo '</ss:Row>';
        }

        //
        echo '</ss:Table>
                </Worksheet>
                </Workbook>';

        return is_null($dir) ? true : self::putObToFile($dir . $filename);
    }

    /**
     * 通过CSV输出excel
     * @return      void
     * @see         self::export2DArray
     */
    public static function export2DArrayByCSV($data_list, $map, $file_name, $dir = null, $output_title = true, $append = false)
    {
        if (is_null($dir)) {
            ob_end_clean();
            self::setHeader($file_name);
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
        } else {
            ob_start();
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
        }

        // 输出表头
        $line_break = self::getLineBreak();
        $len = sizeof($map);
        if ($output_title) {
            $k = 1;
            foreach ($map as $field) {
                $str = $field['title'];
                echo $str;
                if ($k !== $len) {
                    echo ",";
                }
                ++$k;
            }
            echo $line_break;
        }
        // 输出数据
        foreach ($data_list as $v) {
            $k = 1;
            foreach ($map as $field) {
                $str = "\"" . $v[$field['field']] . "\"";
                echo $str;
                if ($k !== $len) {
                    echo ",";
                }
                ++$k;
            }
            echo $line_break;
        }

        return is_null($dir) ? true : self::putObToFile($dir . $file_name, $append);
    }

    /**
     * 通过CSV 循环输入数据最后输出excel
     *
     * @return      void
     * @see         self::export2DArray
     * @author      yuhui
     */
    public static function export2DArrayByCSVLoop($data_list, $map, $file_name, $dir = null, $output_title = true, $append = false, $out = false)
    {
        if (is_null($dir)) {
            @ob_end_clean();
            if (!empty($file_name)) {
                self::setHeader($file_name);
                echo "\xEF\xBB\xBF"; // UTF-8 BOM
            }
        } else {
            @ob_start();
        }

        // 输出表头
        $line_break = self::getLineBreak();
        $len = sizeof($map);
        if ($output_title) {
            $k = 1;
            foreach ($map as $field) {
                $str = $field['title'];
                echo $str;
                if ($k !== $len) {
                    echo ",";
                }
                ++$k;
            }
            echo $line_break;
        }

        // 输出数据
        foreach ($data_list as $v) {
            $k = 1;
            foreach ($map as $field) {
                $str = "\"" . $v[$field['field']] . "\"";
                echo $str;
                if ($k !== $len) {
                    echo ",";
                }
                ++$k;
            }
            echo $line_break;
        }

        //不输出的时候只追加数据；
        if ($out) {
            return is_null($dir) ? true : self::putObToFile($dir . $file_name, $append);
        }
    }

    /**
     * 将ob缓冲数据写入文件
     */
    protected static function putObToFile($file_path, $append = false)
    {
        $dir = dirname($file_path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $content = ob_get_contents();
        ob_end_clean();
        return file_put_contents($file_path, $content, $append ? FILE_APPEND : null);
    }

    /**
     * 超过excel导出限制的用zip导出
     * @param array $arr 数组数组
     * @param array $map 数组字段映射数组
     * @param string $filename 文件名，传入zip的文件名如book.zip
     * @param int $col_width 列宽度，注意不同的方法生成传入的长度单位是不一样的
     * @param string $func 调用本类中哪个静态方法生成excel
     * @return      void
     */
    /*
    public static function export2DArrayByZip($arr, $map, $filename, $col_width, $func, $dir = null)
    {
        $per_size  = 60000;
        $lenth     = sizeof($arr);

        // 清空、创建临时文件夹
        $user_id = DataCommon::getSessionUserId();
        $user_id = empty($user_id) ? 0 : $user_id;
        $tmp_path  = empty($dir) ? __ROOT__.self::$zip_path.$user_id."/" : $dir;

        if (!FileHelper::isDirWriteable($tmp_path))
        {
            Response::parentShowMsgBar('error', '文件生成失败');
        }
        if (!FileHelper::deleteFile($tmp_path))
        {
            Response::parentShowMsgBar('error', '文件生成失败');
        }
        mkdir($tmp_path, 0777, true);

        // 传入方法是否可调用，进一步严格判断可用反射
        if (!in_array($func, get_class_methods('ExcelHelper'), true))
        {
            Response::parentShowMsgBar('error', '生成excel方法不存在');
        }

        // 分解数据数组
        $chunk_arr = array_chunk($arr, $per_size);
        foreach ($chunk_arr as $k => &$data)
        {
            // 调用传入方法生成文件
            $tmp_name = ($k + 1).($func === 'export2DArrayByCSV' ? '.csv' : '.xls');
            call_user_func_array("ExcelHelper::{$func}", array($data, $map, $tmp_name, $col_width, $tmp_path));
        }
        unset($data);

        // 打包文件
        $PHPZip = new PHPZip();
        if (empty($dir))
        {
            $filename = self::backCorrectString($filename);
            $PHPZip->ZipAndDownload($tmp_path, $filename);
        }
        else
        {
            $PHPZip->Zip($tmp_path, $tmp_path.$filename);
        }
        return true;
    }
     */

    /**
     * 按行读取excel文件数据
     * `$fields`设置读取列字段的名称
     *
     * @param       $excelPath
     * @param int $sheet 设置excel读取第几个工作表，从0开始
     * @param string $rowRange 读取的行范围，起始行与结束行以逗号分隔，若起始行与结束行为空则分别设置1或最大行数
     * ~~~
     * ~~~
     * $colRange = 'A:' 从第A列读到最后一列数据
     *
     * $colRange = 'A:Z' 从第A列读到第Z列数据
     * ~~~
     *
     * @param string $colRange 读取的列范围，起始列与结束列以逗号分隔，若起始列与结束列为空则分别设置A或最大列数
     * ~~~
     * $rowRange = '1:'  从第1行读到最后一行数据
     *
     * $rowRange = '1:10' 从第1行读到第10行数据
     * ~~~
     *
     * @param array $fields 设置列数据的key
     * ~~~
     * ['A' => 'name', 'B' => 'age', 'C' => 'sex']  指定每一列对应的key
     * ~~~
     *
     * ~~~
     * // 可以对列数据设置处理规则
     * // skipOnEmpty: 是否在该列下的单元格值为空时(使用isEmpty判断), 丢弃该单元格所在行
     * // format: 对单元格值进行格式化或转义(如设置为`%html`时，则使用Html::encode进行转义)处理
     *
     * ['A' => ['name', 'skipOnEmpty' => true, 'format' => '%s'], 'B" => ['age', 'format' => '%d']]
     * ~~~
     *
     * @return array
     * @throws \PHPExcel_Reader_Exception
     * @author Hulifa
     */
    public static function readFile($excelPath, $sheet = 0, $rowRange = '1:', $colRange = 'A:', array $fields = [])
    {
        require_once Yii::$app->basePath . '/extensions/PHPExcel/PHPExcel/IOFactory.php';

        $excelReader = \PHPExcel_IOFactory::createReader('Excel2007');
        if (!$excelReader->canRead($excelPath)) {
            $excelReader = \PHPExcel_IOFactory::createReader('Excel5');
            if (!$excelReader->canRead($excelPath)) {
                throw new \PHPExcel_Reader_Exception('this excel file:' . $excelPath . 'can\'t be read.');
            }
        }

        $PHPExcel = $excelReader->load($excelPath);

        $currentSheet = $PHPExcel->getSheet($sheet);

        list($startRow, $endRow) = explode(':', (string)$rowRange);
        list($startCol, $endCol) = explode(':', (string)$colRange);

        $startRow = empty($startRow) ? 1 : $startRow;
        $startCol = empty($startCol) ? 'A' : $startCol;

        /**取得最大的列号*/
        $endCol = empty($endCol) ? $currentSheet->getHighestColumn() : $endCol;
        $endCol++;//列号+1
        /**取得一共有多少行*/
        $endRow = empty($endRow) ? $currentSheet->getHighestRow() : $endRow;

        $result = [];

        //循环读取每个单元格的内容。注意行从1开始，列从A开始
        for ($rowIndex = $startRow; $rowIndex <= $endRow; $rowIndex++) {
            for ($colIndex = $startCol; $colIndex != $endCol; $colIndex++) {
                $addr = $colIndex . $rowIndex;
                $cell = $currentSheet->getCell($addr)->getValue();
                if ($cell instanceof \PHPExcel_RichText) {
                    //富文本转换字符串
                    $cell = $cell->__toString();
                } elseif ($cell[0] == '=') {
                    // Calculated data
                    $cell = $currentSheet->getCell($addr)->getCalculatedValue();
                }

                //var_dump($fields[$colIndex]);
                if ($fields !== [] && isset($fields[$colIndex])) {
                    // apply rules to cell value, and discard the row data if return false
                    if (self::rulesCellValue($fields[$colIndex], $cell, $result[$rowIndex]) === false) {
                        unset($result[$rowIndex]);
                        break 1;
                    }
                } else {
                    $result[$rowIndex][] = trim($cell);
                }
            }
        }

        return $result;
    }

    /**
     * 对单元格数据按规则处理
     *
     * @param $rules
     * @param string $cellValue 单元格内容值
     * @param array $rowData 单元格所在行的数据
     * @return bool             是否skipOnEmpty
     * @author Hulifa
     */
    public static function rulesCellValue($rules, $cellValue, &$rowData)
    {
        if (is_string($rules)) {
            $rowData[$rules] = trim($cellValue);
            return true;
        }

        if (is_array($rules)) {
            if (isset($rules[0]) && is_string($rules[0])) {
                $colKey = $rules[0];
                unset($rules[0]);
            }

            // skipOnEmpty
            if (!empty($rules['skipOnEmpty']) && self::isEmpty($cellValue)) {
                return false;
            }

            // format
            if (!empty($rules['format'])) {
                if (!empty($colKey)) {
                    $rowData[$colKey] = self::formatCellValue($cellValue, $rules['format']);
                } else {
                    $rowData[] = self::formatCellValue($cellValue, $rules['format']);
                }
                return true;
            }

            $rowData[] = trim($cellValue);
        }

        return true;
    }

    /**
     * 格式化单元格值
     *
     * @param $cellValue
     * @param $format
     * @return string
     * @author Hulifa
     */
    public static function formatCellValue($cellValue, $format)
    {
        switch ($format) {
            case '%html':
                return \yii\helpers\Html::encode($cellValue);
                break;
            case '%date' :
                $len = strlen($cellValue);
                if ($len == 4) {
                    return $cellValue;
                }
                if ($len == 6) {
                    $year = substr($cellValue, 0, 4);
                    $month = substr($cellValue, 4, 2);
                    return $year . '-' . $month;
                }
                if ($len == 8) {
                    $year = substr($cellValue, 0, 4);
                    $month = substr($cellValue, 4, 2);
                    $day = substr($cellValue, 6, 2);
                    return $year . '-' . $month . '-' . $day;
                }
                return false;
            default:
                return sprintf($format, $cellValue);
                break;
        }
    }

    /**
     * 判定给定值是否为空
     *
     * @param      $value
     * @param bool $isTrim 是否对字符串去掉前后空格后判断
     * @return bool
     * @author Hulifa
     */
    public static function isEmpty($value, $isTrim = true)
    {
        if ($isTrim && is_string($value)) {
            $value = trim($value);
        }

        return $value === null || $value === [] || $value === '';
    }

    /**
     * 写入excel文件数据
     *
     * @param array $title
     * ~~~
     * $title = ['套餐ID', 'SKU', '商品名称'];
     * ~~~
     *
     * ~~~
     * // 可以对title设置format来指定单元格格式，可设置的值查看[\PHPExcel_Cell_DataType类]
     * $title = ['套餐ID', ['SKU', 'format' => \PHPExcel_Cell_DataType::TYPE_NUMERIC], '商品名称'];
     * ~~~
     *
     * @param array $rows
     * @param string $startCol 开始写入列
     * @param int $startRow 开始写入行
     * @param array $style 设置title字段样式
     *
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     * @author Hulifa
     */
    public static function writeFile(array $title, array $rows, $startCol = 'A', $startRow = 1, array $style = [])
    {
        require_once Yii::$app->basePath . '/extensions/PHPExcel/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();

        // Title style
        $style = array_replace_recursive(['font' => ['bold' => true, 'size' => 12], 'merge' => 1], $style);

        // 表头合并行数
        $titleMerge = (int)$style['merge'];
        $titleMerge = $titleMerge < 1 ? 1 : $titleMerge;

        // merge not belong style setting
        unset($style['merge']);

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("小米buy系统")
            ->setLastModifiedBy("小米buy系统");
        // ->setTitle("")
        // ->setSubject("PHPExcel Test Document")
        // ->setDescription("Test document for PHPExcel, generated using PHP classes.")
        // ->setKeywords("office PHPExcel php")
        // ->setCategory("Test result file");

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Get work sheet
        $worksheet = $objPHPExcel->getActiveSheet();

        // cell data type setting
        $colFormat = [];

        // Set sheet title
        if (!empty($title)) {
            // base 0!!!
            $currentColIndex = \PHPExcel_Cell::columnIndexFromString($startCol) - 1;
            foreach ($title as $item) {
                $cell = \PHPExcel_Cell::stringFromColumnIndex($currentColIndex) . $startRow;

                if (is_array($item)) {
                    $colFormat[$currentColIndex] = isset($item['format']) ? $item['format'] : '';
                    $item = $item[0];
                }

                $worksheet->setCellValue($cell, $item);
                if ($titleMerge > 1) $worksheet->mergeCells($cell . ':' . \PHPExcel_Cell::stringFromColumnIndex($currentColIndex) . ($startRow + $titleMerge - 1));
                $currentColIndex++;
            }

            // Set sheet title style
            $titleStyle = (new \PHPExcel_Style())->applyFromArray($style);
            $worksheet->setSharedStyle($titleStyle, "{$startCol}{$startRow}:{$cell}");
        }

        // Set Content rows
        $currentRow = $startRow + $titleMerge;
        $currentColIndex = 0;
        foreach ($rows as $row) {
            $currentColIndex = \PHPExcel_Cell::columnIndexFromString($startCol) - 1;
            foreach ($row as $item) {
                $cell = \PHPExcel_Cell::stringFromColumnIndex($currentColIndex) . $currentRow;
                // $worksheet->setCellValue($cell, $item);

                if (!empty($colFormat[$currentColIndex])) {
                    $worksheet->setCellValueExplicit($cell, $item, $colFormat[$currentColIndex]);
                } else {
                    $worksheet->setCellValue($cell, $item);
                }

                $currentColIndex++;
            }
            $currentRow++;
        }

        // Set Auto column width
        // for ($i = 0; $i < $currentColIndex; $i++) {
        //     $worksheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
        // }

        return $objPHPExcel;
    }

    /**
     * 下载excel文件
     *
     * @param        $objPHPExcel
     * @param string $fileName 下载文件名称
     * @param string $type excel文件类型 `Excel5` or `Excel2007`
     * @throws \PHPExcel_Reader_Exception
     * @author Hulifa
     */
    public static function downloadFile($objPHPExcel, $fileName, $type = 'Excel5')
    {
        require_once Yii::$app->basePath . '/extensions/PHPExcel/PHPExcel/IOFactory.php';

        $type = in_array($type, ['Excel5', 'Excel2007'], true) ? $type : 'Excel2007';

        // Set file extension name
        $fileName .= $type === 'Excel5' ? '.xls' : '.xlsx';

        // Set file content type
        $contentType = $type === 'Excel5' ? 'application/vnd.ms-excel' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $type);
        $objWriter->save('php://output');

        // end the application
        Yii::$app->end();
    }

    /**
     * 判断用户客户端操作系统是否是windows的操作系统。
     * @return boolean
     * @since  12-8-6 下午4:07
     * @author yuhui
     */
    public static function isWindowsOs()
    {
        //获得用户操作系统
        $system = array(
            'Windows 8' => 'NT 6.2',
            'Windows 7' => 'NT 6.1',
            'Windows Vista' => 'NT 6.0',
            'Windows 2003' => 'NT 5.2',
            'Windows XP' => 'NT 5.1',
            'Windows 2000' => 'NT 5',
            'Windows ME' => '4.9',
            'Windows NT 4' => 'NT 4',
            'Windows 98' => '98',
            'Windows 95' => '95',
            'Windows 10' => 'NT 10.0',
        );

        foreach ($system as $k => $v) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], $v) !== false) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 判断字符是否是utf-8的编码。
     * @param string $str 字符串
     * @return boolean
     * @author yuhui
     * @since  12-10-31 下午5:09
     */
    public static function isUtf8Encoding($str)
    {
        if (!is_string($str)) {
            return FALSE;
        }
        return mb_detect_encoding($str, "UTF-8", TRUE);
    }

    /**
     * 根据操作系统返回相应的字符串[window -> GBK, LINUX -> UTF-8]
     * @param string $str
     * @return string
     * @author Cash yu <yuhui@xiaomi.com>
     * @since  12-10-31 下午5:22
     */
    public static function backCorrectString($str)
    {
        //判断是否是WINDOWS的系统
        $is_windows = self::isWindowsOs();
        $is_utf8 = self::isUtf8Encoding($str);

        //windows
        if ($is_windows) {
            return $is_utf8 ? iconv("UTF-8", "GBK//TRANSLIT//IGNORE", $str) : $str;
        } //非windows
        else {
            return $is_utf8 ? $str : iconv("GBK", "UTF-8//TRANSLIT//IGNORE", $str);
        }
    }

    public static function getLineBreak()
    {
        $is_windows = self::isWindowsOs();
        return $is_windows ? "\r\n" : "\n";
    }


    /**
     * mergeCells
     *
     * @param mixed $cellMerge
     * @param mixed $objPHPExcel
     *
     * @return void
     */
    public static function mergeCells(array $cellMerge, \PHPExcel $objPHPExcel)
    {
        foreach ($cellMerge as $cell) {
            $objPHPExcel->getActiveSheet()->mergeCells($cell);
        }
    }

    /**
     * getExcelObject
     *
     *
     * @return void
     */
    public static function getExcelObject()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // 左右居中
        $objPHPExcel->setActiveSheetIndex()->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); // 上下居中
        $objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

        return $objPHPExcel;
    }


    /**
     * setCellValue
     * @return void
     */
    public static function setCellValues(array $cellValues, $objPHPExcel)
    {
        foreach ($cellValues as $key => $cellValue) {
            $objPHPExcel->getActiveSheet()->setCellValue($key, $cellValue);
        }
    }

    /**
     * setCellValueExplicit
     *
     * @param mixed $cellValue
     * @param mixed $objPHPExcel
     *
     * @return void
     */
    public static function setCellValueExplicit(array $cellValues, $objPHPExcel)
    {
        foreach ($cellValues as $key => $cellValue) {
            $objPHPExcel->getActiveSheet()->setCellValueExplicit($key, $cellValue);
        }
    }

    /**
     * excelFileResponse
     *
     *
     * @return void
     */
    public static function excelFileResponse($fileName, $objPHPExcel, $excelType = 'Excel5')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $excelType);
        $objWriter->save('php://output');
    }

    public static function getColume($colume)
    {
        return \PHPExcel_Cell::stringFromColumnIndex($colume);
    }
}