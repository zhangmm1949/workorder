<?php
namespace app\models\common\csv;

trait Parse {

    /**
     * 主方法
     *
     * @param $stringCsvData string CSV 字符串
     * @param array $keys 可选的CSV数据的列名 数组
     * @param null $delimiter CSV 列的分隔符
     * @param null $enclosure CSV数据单元格的引用符
     * @param string $escape CSV数据的转义符
     * @return mixed array
     * @throws \Exception
     */
    public static function Parse($stringCsvData, array $keys = [], $delimiter = null, $enclosure = null, $escape = '\\') {
        // 先找一个在临时目录下不存在的文件名
        for (
            $intCount = 0,
            $stringTempFilePath = '/tmp/Yii.X.Data.ParseCsv.' . dechex(crc32(microtime(true))) . '.' . $intCount . '.tmp';
            file_exists($stringTempFilePath);
            ++$intCount
        );
        // 将 CSV 文本放入这个临时文件
        if (false === @ file_put_contents($stringTempFilePath, $stringCsvData)) {
            // 如果写入失败, 则报错
            throw new \Exception('X.DataParseCsv write temporary file failed: ' . $stringTempFilePath);
        }
        // 最后用现成的 ParseFile 方法来处理
        $result = self::ParseFile($stringTempFilePath, $keys, $delimiter, $enclosure, $escape);
        // 删除临时文件
        @ unlink($stringTempFilePath);
        // 返回解析结果
        return $result;
    }
}
