<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\common\TagNamesTrait;
use PhpDevil\orm\models\ActiveForm;

class Form extends ActiveForm
{
    use OrmModelsTrait;
    use TagNamesTrait;
}