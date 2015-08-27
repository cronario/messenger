<?php

namespace Messenger;

class Template
{
    protected $_locale;
    protected $_vars;
    protected $_template;
    protected $_layout;

    protected $_templateDir;
    protected $_div = ['{', '}'];
    protected static $_cache = [];
    protected static $templateDir;


    // region Configuration ********************************************************

    /**
     * @param $templateDir
     */
    public static function setTemplateDir($templateDir)
    {
        self::$templateDir = $templateDir;
    }

    /**
     * @return string|null
     */
    public static function getTemplateDir()
    {
        return self::$templateDir;
    }

    // endregion ***************************************************************

    /**
     * @param      $template
     * @param      $vars
     * @param      $locale
     * @param null $dir
     */
    public function __construct($template, $vars, $locale, $dir = null)
    {
        if(null != $dir){
            self::setTemplateDir($dir);
        }
        $this->setTemplate($template);
        $this->setVars($vars);
        $this->setLocale($locale);
    }

    /**
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $file = 'locale.' . $locale;

        if (static::$_cache[$file]) {
            $this->_locale = static::$_cache[$file];

            return $this;
        }

        $lang = $this->load('locale.' . $locale);

        foreach ($lang as $key => $value) {
            $this->_locale[$this->_div[0] . $key . $this->_div[1]] = $value;
        }

        static::$_cache[$file] = $this->_locale;

        return $this;
    }

    /**
     * @param array $vars
     *
     * @return $this
     */
    public function setVars(array $vars = [])
    {
        foreach ($vars as $key => $value) {
            $this->_vars[$this->_div[0] . $key . $this->_div[1]] = $value;
        }

        return $this;
    }

    /**
     * @param $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        if (static::$_cache[$template]) {
            $this->_template = static::$_cache[$template];
            $this->_layout = static::$_cache[$this->_template['layout']];

            return $this;
        }

        $this->_template = $this->load($template);
        $layout = $this->_template['layout'];

        $this->_layout = $this->load($layout);


        $replace = [];
        foreach ($this->_template as $key => $value) {
            $replace[$this->_div[0] . $key . $this->_div[1]] = $value;
        }

        $this->_template = $replace;
        static::$_cache[$template] = $replace;
        static::$_cache[$layout] = $this->_layout;

        return $this;
    }

    /**
     * @param $path
     *
     * @return mixed|null
     * @throws TemplateException
     */
    public function load($path)
    {

        $file = self::getTemplateDir() . $path . '.php';

        if (is_file($file)) {
            return include_once($file);
        }

        throw new TemplateException("Template cant find template file : {$file}");
    }

    /**
     * @return array
     */
    public function make()
    {
        $data = json_encode($this->_layout);
        $data = strtr($data, $this->_template);
        $data = strtr($data, $this->_locale);
        $data = strtr($data, $this->_vars);
        $data = json_decode($data, true);

        return $data;
    }

}

