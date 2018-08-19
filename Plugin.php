<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;


/**
 * 文章和全站字数统计
 *
 * @package WordsCounter
 * @author Elatis
 * @version 1.0.0
 * @link https://ela.moe
 */

class WordsCounter_Plugin implements Typecho_Plugin_Interface
{

    /**
     * 启用插件方法,如果启用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        // TODO: Implement activate() method.
        Typecho_Plugin::factory('Widget_Archive')->___charactersNum = array('WordsCounter_Plugin','charactersNum');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        // TODO: Implement deactivate() method.
    }

    /**
     * 获取插件配置面板
     *
     * @static
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // TODO: Implement config() method.
        $showPrivate = new Typecho_Widget_Helper_Form_Element_Radio('showPrivate',
            array(0 => _t('不计算'), 1 => _t('计算') ) ,'0', _t('计算特殊文章'), _t('全站字数是否计算非公开文章字数'));
        $form->addInput($showPrivate);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
        // TODO: Implement personalConfig() method.
    }

    /**
     * 单个文章字数
     *
     * @access public
     * @param $archive
     * @return int
     */
    public static function charactersNum($archive) {
        return mb_strlen($archive->text,'UTF-8');
    }

    /**
     * 输出全站字数
     *
     * @access public
     * @return void
     * @throws Typecho_Db_Exception
     * @throws Typecho_Exception
     */
    public static function allOfCharacters() {
        $pluginOpts = Typecho_Widget::widget('Widget_Options')->plugin('WordsCounter');
        $showPrivate = intval($pluginOpts->showPrivate);

        $chars = 0;
        $db = Typecho_Db::get();
        if($showPrivate == 0){
            $select = $db ->select('text')
                        ->from('table.contents')
                        ->where('table.contents.status = ?','publish');
        } else {
            $select = $db ->select('text')
                        ->from('table.contents');
        }

        $rows = $db->fetchAll($select);
        foreach ($rows as $row){
            $chars += mb_strlen($row['text'], 'UTF-8');
        }

        $unit = '';
        if($chars >= 10000) {
            $chars /= 10000;
            $unit = 'W';
        } else if($chars >= 1000) {
            $chars /= 1000;
            $unit = 'K';
        }

        $out = sprintf('%.2lf %s',$chars, $unit);

        echo $out;
    }
}