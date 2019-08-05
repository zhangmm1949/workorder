<?php


/**
 * implode 的加引号版本
 *
 * @author chengjinyong
 * @version 2015/06/17 11:20
 * @param string $glue 分隔符
 * @param array $array 字符串数组
 * @param string $quote 引号字符
 * @param int $options 参数：1=是否转义，2=适应 SQL NULL
 */
function implode_with_quote($glue, $array, $quote = '"', $options = 0) {
    $result = array();
    foreach ($array as $key => $value) {
        if ($options & 2 && $value === null) {
            $result[$key] = 'null';
        } else {
            $result[$key]
                = $quote
                . ($options & 1 ? addcslashes($value, '\\' . $quote) : $value)
                . $quote;
        }
    }
    return implode($glue, $result);
}

/**
 * 格式化输出 （xdebug效果）
 * @param $data
 * @param string $type
 */
function vd($data, $type = '')
{
    echo '<pre>';
    if (empty($data)) {
        var_dump($data);
    } else {
        if (!empty($type) && function_exists($type)) {
            $type($data);
        } else {
            if (is_array($data)) {
                print_r($data);
            } else {
                var_dump($data);
            }
        }
    }
}

function vde($data, $type = '')
{
    vd($data, $type);
    exit;
}