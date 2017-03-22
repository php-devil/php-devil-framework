<?php
namespace PhpDevil\framework\base;

abstract class ApplicationPrototype extends ModulePrototype
{
    public function loadModule($id)
    {
        if (isset($this->config['modules'][$id])) {
            $moduleConfig = $this->config['modules'][$id];
            if (isset($moduleConfig['class'])) {
                $className = $moduleConfig['class'];
                unset($moduleConfig['class']);
                return new $className($moduleConfig);
            }
        }
        return false;
    }
}