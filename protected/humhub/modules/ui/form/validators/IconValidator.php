<?php



namespace humhub\modules\ui\form\validators;

use humhub\modules\ui\form\widgets\IconPicker;
use humhub\modules\ui\icon\widgets\Icon;
use Yii;
use yii\base\Model;
use yii\validators\Validator;

/**
 * IconValidator validates input from the IconPicker
 *
 * @since 1.3
 * @see IconPicker
 */
class IconValidator extends Validator
{
    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $iconPicker = new IconPicker(['model' => $model, 'attribute' => $attribute]);

        if (!in_array($model->$attribute, Icon::$names)) {
            $this->addError($model, $attribute, Yii::t('UiModule.form', 'Invalid icon.'));
        }
    }

}
