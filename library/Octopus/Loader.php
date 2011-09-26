<?php
/**
 * Octopus Loader
 *
 * @author milan
 *
 * Implements Singleton pattern
 */
class Octopus_Loader
{
    /** singleton implementation */
    protected static $_instance = null;

    protected static $_libraryPath = "";
	

    /**
     * private constructor - should not be called, call singleton's "activate" instead
     */
    private function  __construct($libraryPath)
    {
        self::$_libraryPath = $libraryPath;
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * returns singleton instance of IVR_Controller
     */
    public static function activate($libraryPath)
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self($libraryPath);
        }
        return self::$_instance;
    }

    /**
     * Autoload a class
     *
     * @param  string $class
     * @return bool
     */
    public static function autoload($className)
    {
        $relativePath = str_replace('_',DIRECTORY_SEPARATOR,$className) . '.php';

        if(is_file(self::$_libraryPath . DIRECTORY_SEPARATOR . $relativePath))
        {
            include_once self::$_libraryPath . DIRECTORY_SEPARATOR . $relativePath;
        }
        else
        {
            return false;
        }
        // does the class requested actually exist now?
        return class_exists($className);
    }
}

/* vi: set ts=8 sw=4 sts=4 noet: */