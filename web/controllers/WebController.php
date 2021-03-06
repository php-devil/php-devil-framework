<?php
namespace PhpDevil\framework\web\controllers;
use PhpDevil\framework\base\ApplicationInterface;
use PhpDevil\framework\base\Controller;
use PhpDevil\framework\base\ModulePrototype;
use PhpDevil\framework\components\page\Renderable;
use PhpDevil\framework\models\ModelInterface;
use PhpDevil\framework\models\StdForm;
use PhpDevil\framework\web\widgets\ConfirmWidget;
use PhpDevil\framework\web\widgets\FormWidget;
use PhpDevil\framework\web\widgets\GridWidget;

class WebController extends Controller implements Renderable
{
    public $pageHeading = '';

    /**
     * Счетчик генератора уникальных ID блоков страницы
     * @var int
     */
    private static $uniqueBlockID = 0;

    /**
     * Сгенерировать новый уникальный ID для блока
     * @return string
     */
    final public static function getUniqueBlockID()
    {
        ++ self::$uniqueBlockID;
        return 'page_block_' . self::$uniqueBlockID;
    }

    /**
     * Путь к директории представлений
     * @return mixed|string
     */
    public function getViewsLocation()
    {
        if ($this->owner instanceof ApplicationInterface) {
            return \Devil::getPathOf('@app') . '/views';
        } else {
            return str_replace('\\', '/', $this->owner->getLocation() . '/views');
        }
    }

    public function confirmWidget($modelClass, $subAction, $config = [])
    {
        $widget = null;
        if (isset($config['entityID']) && ($model = $modelClass::findByPK($config['entityID']))) {
            $widget = new ConfirmWidget($model, $subAction, $config);
            $view = '//widgets/modal/confirm';
        } else {
            $view = '//widgets/errors/404';
        }
        ob_start();
        $this->render($view, ['confirm' => $widget]);
        return ob_get_clean();
    }

    /**
     * Дефолтный виджет формы
     *
     * @param $modelClass
     * @param $subAction
     * @param array $config
     * @param bool $isNew
     * @return string
     */
    public function formWidget($modelClass, $subAction, $config = [], $isNew = false)
    {
        \Devil::app()->db;
        $widget = null;
        if (null === $subAction || $modelClass::accessControlStatic($subAction)) {
            $view = '//widgets/forms/default';
            if (isset($config['view'])) $view = $config['view'];
            if ($isNew || ($modelClass instanceof StdForm)) {
                $model = $modelClass::model();
            } else {
                $model = $modelClass::findByPK($config['entityID']);
                if (!$model->accessControl($subAction)) $view = '//widgets/errors/403';
            }

            if (isset($config['autosave']) && 'post' === $config['autosave']) {
                $model->saveFromPost();
            }

            if ($model) {
                $widget = new FormWidget($model, $config);
            } else {
                $view = '//widgets/errors/404';
            }
        } else {
            $view = '//widgets/errors/403';
        }
        ob_start();
        $this->render($view, ['form' => $widget]);
        return ob_get_clean();
    }

    /**
     * Отображение в виде таблицы
     *
     * @param $provider
     * @param $subAction
     * @param null $config
     * @return string
     */
    public function gridWidget($provider, $subAction, $config = null)
    {
        $widget = null;
        $view = '//widgets/grids/default';
        if (isset($config['view'])) $view = $config['view'];

        ob_start();
        $this->render($view, ['grid' => new GridWidget($provider, $config)]);
        return ob_get_clean();
    }

    public function render($view, $attributes = [], $display = true)
    {
        if (false === strpos($view, '//')) {
            if ($tag = $this->getTagName()) $view = $tag . '/' . $view;
        } else {
            $view = substr($view, 2);
        }
        return \Devil::app()->page->render($this, $view, $attributes, $display);
    }
}