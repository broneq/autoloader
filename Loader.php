<?php


namespace Broneq\Autoloader;

/**
 * Class Loader
 */
class Loader
{
    const NAMESPACE_SEPARATOR = '\\';

    private $registered = false;
    private $namespaces = [];
    private $classes = [];
    private $files = [];

    /**
     * Registers Namespace pointing to directory
     * @param string $namespace
     * @param string $path
     * @return self
     */
    public function registerNamespace($namespace, $path)
    {
        $this->namespaces[$this->fixClassName($namespace)] = $path;
        return $this;
    }

    /**
     * Register Class pointing to single file
     * @param string $classNamespace namespace of class
     * @param string $fileName filename of class
     * @return self
     */
    public function registerClass($classNamespace, $fileName)
    {
        $this->classes[$this->fixClassName($classNamespace)] = $fileName;
        return $this;
    }

    /**
     * Register filename - it's working like include
     * @param string $fileName
     * @return self
     */
    public function registerFile($fileName)
    {
        $this->files[] = $fileName;
        return $this;

    }

    /**
     * Load class by namespace
     * @param string $className
     * @return bool
     */
    public function load($className)
    {
        $fixedClassName = $this->fixClassName($className);
        return $this->processClasses($fixedClassName) || $this->processNamespaces($fixedClassName);
    }

    /**
     * Register autoloader definitions
     * @param bool $prepend If true, spl_autoload_register() will prepend the autoloader on the autoload stack instead of
     * appending it.
     * @return bool
     * @throws \Exception
     */
    public function register($prepend = true)
    {
        if (!$this->registered) {
            $this->processFiles();
            spl_autoload_register([$this, "load"], true, $prepend);
            $this->registered = true;
            return true;
        }
        return false;
    }

    /**
     * Unregister autoloader definitions
     * @return bool
     */
    public function unregister()
    {
        if ($this->registered) {
            spl_autoload_unregister([$this, "load"]);
            $this->registered = false;
            return true;
        }
        return false;
    }

    /**
     * Fix class name separator
     * @param string $className
     * @return string
     */
    private function fixClassName($className)
    {
        return ltrim($className, self::NAMESPACE_SEPARATOR);
    }

    /**
     * Loads class from namespaces definition if exist
     * @param $namespace
     * @return bool
     */
    private function processNamespaces($namespace)
    {
        foreach ($this->namespaces as $registeredNamespace => $path) {
            if (strpos($namespace, $registeredNamespace) === 0) {
                return $this->includeFile($this->namespaceToPath($registeredNamespace, $path, $namespace) . '.php');
            }
        }
        return false;
    }

    /**
     * Converts namespace to path
     * @param string $className
     * @return string
     */
    private function namespaceToPath($registeredNamespace, $registeredDirectory, $namespace)
    {
        $strippedNs = str_replace($registeredNamespace . self::NAMESPACE_SEPARATOR, '', $namespace);
        return rtrim($registeredDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace(self::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $strippedNs);
    }

    /**
     * Loads class from classes definition if exist
     * @param string $className
     * @return bool
     */
    private function processClasses($className)
    {
        if (array_key_exists($className, $this->classes)) {
            return $this->includeFile($this->classes[$className]);
        }
        return false;
    }

    /**
     * Loads files from definition
     * @throws \Exception
     */
    private function processFiles()
    {
        foreach ($this->files as $file) {
            if (!$this->includeFile($file)) {
                throw new \Exception('Can\'t load file: ' . $file);
            }
        }
    }

    /**
     * Include file if exists
     * @param $file
     * @return bool
     */
    private function includeFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
}