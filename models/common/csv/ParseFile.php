<?php
namespace app\models\common\csv;

use Exception;

trait ParseFile {

    /**
     * * 节点
     *
     * 此方法主要依赖 php 自带的 fgetcsv() 函数
     *
     * @param $file_path string CSV文件路径
     * @param array $keys 可选的CSV数据的列名数组
     * @param null $delimiter CSV列的分隔符
     * @param null $enclosure CSV数据单元格的引用符
     * @param string $escape CSV数据的转义符
     * @return array
     * @throws Exception
     */
    public static function ParseFile($file_path, array $keys = [], $delimiter = null, $enclosure = null, $escape = '\\') {
        // 打开 csv 文件的资源句柄, 如果失败则报错
        if (!$fp = @ fopen($file_path, 'r')) {
            throw new Exception('ParseCsvFile Error: Can not open csv file: ' . $file_path);
        }
        // 初始化结果数组
        $result = [];
        // 初始化列数计数器
        $columnsCount = null;
        // 如果没有传入引号符, 则尝试分析识别
        if (!isset($enclosure)) {
            // 先取出 csv 的第一行并剔除左侧潜在的空白字符
            $firstRow = ltrim(fgets($fp));
            // 为下方实际做 fgetcsv() 而将数据指针重置到 0 位置
            fseek($fp, 0);
            // 检查并在存在的情况下移除 BOM 头
            if (substr($firstRow, 0, 3) === "\xEF\xBB\xBF") {
                $firstRow = ltrim(substr($firstRow, 3));
            }
            // 分析第一个字符
            switch ($firstRow[0]) {
                case '\'':
                case '`':
                case '"':
                    // 发现是以上三种引号之一
                    $enclosure = $firstRow[0];
                    break;
                default:
                    // 未发现引号符
                    // 注意:
                    // 不要在这里默认赋双引号给 $enclosure
                    // 因为下面要以 $enclosure 是否有值来决定怎么寻找分隔符
            }
        }
        
        // 如果没有传入分隔符, 则尝试分析识别
        if (!isset($delimiter)) {
            if (!isset($enclosure)) {
                // 如果之前没有找到等效引号的符号, 那就尝试用几种常用的符号来确定分隔符
                // 如果没找到, 就保持分隔符为 null
                if (preg_match('/[ \t|;,]/', $firstRow, $match)) {
                    $delimiter = $match[0];
                }
                // 确认分隔符后, 将引号符初始化为双引号, 也即 fgetcsv() 默认值
                $enclosure = '"';
                
            } else {
                // 否则如果之前有找到等效引号的符号, 那就以这个符号的右边界的下一个字符来作为分隔符
                // 如果上方没有进入引号符识别流程中, 则需要重新获取 csv 的第一行样本数据
                if (!isset($firstRow)) {
                    $firstRow = ltrim(fgets($fp));
                    fseek($fp, 0);
                    // 检查并在存在的情况下移除 BOM 头
                    if (substr($firstRow, 0, 3) === "\xEF\xBB\xBF") {
                        $firstRow = ltrim(substr($firstRow, 3));
                    }
                }
                // 计算行长
                $firstRowLength = strlen($firstRow);
                // 逐个字符递进分析
                for ($searchPos = 1; $searchPos < $firstRowLength; ++$searchPos) {
                    // 寻找下一个右边引号符
                    $rightEnclosurePos = strpos($firstRow, $enclosure, $searchPos);
                    // 场景 ""
                    // 也即引号中没有内容
                    if ($rightEnclosurePos === 1) {
                        // 以右引号的下一个字符作为分隔符
                        // 并将此处提取分界符的逻辑定义为一个 goto 点
                        find_right_enclosure:
                            $delimiter = ltrim(substr($firstRow, $rightEnclosurePos + 1))[0];
                            // 跳出分析循环
                            break;
                    }
                    // 引号内有内容, 可能的场景有:
                    // "asdf\\"
                    // "asdf\\\""
                    // 寻找右引号的前一位字符, 尝试分析是否有转义符
                    for (
                        $times = 0;
                        $times < $rightEnclosurePos && $firstRow[$rightEnclosurePos - $times + 1] === $escape;
                        ++$times
                    );
                    if ($times === 0) {
                        // 没有发现转义符, 则以右引号的下一个字符作为分隔符
                        // 如果右引号后已经没有字符了, 则初始化为分号
                        $delimiter
                            = isset($firstRow[$rightEnclosurePos + 1])
                            ? $firstRow[$rightEnclosurePos + 1]
                            : ';';
                        // 跳出分隔符分析流程
                        break;
                        
                    } elseif ($times % 2 === 0) {
                        // 如果有找到转义符, 且出现的次数是偶数次, 则为转义符的转义符, 类似 \\, 可以无视掉
                        // 跳到之前的处理点, 提取下一个字符作为分隔符, 并跳出分析循环
                        //
                        // 疑问: 加注释的过程中, 发现这段 elseif 是否和上面的 if 是一样的逻辑? 改为共用? 需要再实验
                        //
                        goto find_right_enclosure;
                    }
                }
            }
            // 如果还是找不到分隔符, 则用分号作为默认值
            if (!isset($delimiter)) {
                $delimiter = ';';
            }
        }
        
        //if (!$enclosure) $enclosure = null;
        //var_export([$delimiter, $enclosure, $escape]);exit;
        //var_export(fgetcsv($fp, 0, $delimiter, '"'));
        
        // 逐行解析 csv
        while (false !== $data = fgetcsv($fp, 0, $delimiter, $enclosure, $escape)) {
            // 无效行,跳过
            if (!isset($data[0])) {
                continue;
            }
            // 以下逻辑用于初始化列名数组
            // 如果是第一行
            if (!isset($columnsCount)) {
                // 统计第一行的列数
                $columnsCount = count($data);
                // 如果传入的[列名列表]元素数少于当前第一行的[数据项数量]
                // 则需要为[缺少的列名]以[自然数]来补全
                if (count($keys) < $columnsCount) {
                    // 先将列名列表每项都转成字符串
                    $keys = array_map('strval', $keys);
                    // 从 0 开始看看哪个还没有在 keys 里被占用
                    for ($count = $columnsCount - count($keys), $key = -1; $count > 0; --$count) {
                        while (in_array((string) ++$key, $keys, true));
                        $keys[] = $key;
                    }
                }
                // 尝试检查是否有混入 BOM 头, 有的话将其剔除
                if (substr($data[0], 0, 3) === "\xEF\xBB\xBF") {
                    $data[0] = trim(substr($data[0], 3), $enclosure);
                }
            }
            // 合并列名与数据成为新数组
            if (false === $dataHasKey = @ array_combine($keys, $data)) {
                // 合并失败, 提示数据的列数与应填的列数不一致
                throw new Exception('ParseCsvFile Error: The num of keys different from row columns in csv row: keys[' . count($keys) . '] != cols[' . count($data) . ']: ' . implode(', ', $data));
            }
            // 将合并后的数组放入结果数组
            $result[] = $dataHasKey;
        }
        // 关闭文件资源
        fclose($fp);
        // 返回结果数组
        return $result;
    }
}
