<?php
/**
 * pdf文件操作辅助类
 *
 * @author: Hulifa
 * @date: 2017/9/17 下午8:29
 */

namespace app\models\common;


use kartik\mpdf\Pdf;

class PdfHelper
{
    public static $mode = Pdf::MODE_CORE;

    public static $format = Pdf::FORMAT_A4;

    public static $orientation = Pdf::ORIENT_PORTRAIT;

    public static $destination = Pdf::DEST_BROWSER;

    public static $cssFile = '';

    public static $SetFooter = '{PAGENO}';

    public static $options = [];

    /**
     * 设置为下载pdf方式
     *
     * @author Hulifa
     */
    public static function setDownload()
    {
        self::$destination = Pdf::DEST_DOWNLOAD;
    }

    /**
     * 兼容中文，使中文可正常显示
     *
     * @author Hulifa
     */
    public static function compatibleChinese()
    {
        self::$mode = Pdf::MODE_UTF8;

        self::$options = array_merge(self::$options, ['autoLangToFont' => true,
            'autoScriptToLang' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,]);
    }

    /**
     * 生成pdf文件
     *
     * @param string $content 内容
     * @param string $cssInline 内联样式
     * @param string $title 标签页title
     * @param string $header pdf页头
     * @return \kartik\mpdf\Pdf
     * @author Hulifa
     */
    public static function createPdf($content, $cssInline, $title, $header = '')
    {
        return new Pdf([
            // set to use core fonts only
            'mode' => self::$mode,
            // A4 paper format
            'format' => self::$format,
            // portrait orientation
            'orientation' => self::$orientation,
            // stream to browser inline
            'destination' => self::$destination,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => self::$cssFile,
            // any css to be embedded if required
            'cssInline' => $cssInline,
            // set mPDF properties on the fly
            'options' => array_merge(['title' => $title], self::$options),
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader'=>[$header],
                'SetFooter'=>[self::$SetFooter],
            ]
        ]);
    }
}