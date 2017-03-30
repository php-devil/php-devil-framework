<?php
namespace PhpDevil\framework\base;

use PhpDevil\framework\containers\Modules;

class Module extends ModulePrototype implements ModuleInterface
{
    public static function backendOptions()
    {
        return [];
    }

    /**
     * Проверка разрешений на выполнение модуля
     * @return bool
     */
    public function beforeRun()
    {
        return true;
    }

    /**
     * Выполняется после выполнения метода run модуля
     * только если beforeRun() вернул true
     */
    public function afterRun()
    {

    }

    /**
     * Выполняется вместо run() и afterRun()
     * если beforeRun() вернул false
     */
    public function errorRun()
    {

    }

    public static function module()
    {
        return Modules::container()->load(static::class);
    }
}