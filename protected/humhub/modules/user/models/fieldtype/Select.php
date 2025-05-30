<?php



namespace humhub\modules\user\models\fieldtype;

use humhub\modules\user\models\Profile;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Select handles profile select list fields.
 *
 * @package humhub.modules_core.user.models
 * @since 0.5
 */
class Select extends BaseType
{
    /**
     * @inheritdoc
     */
    public $type = 'dropdownlist';

    /**
     * All possible options.
     * One entry per line.
     * key=>value format
     *
     * @var String
     */
    public $options;

    /**
     * @inerhitdoc
     */
    public $canBeDirectoryFilter = true;

    /**
     * Rules for validating the Field Type Settings Form
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['options'], 'validateListOptions'],
        ];
    }

    /**
     * Returns Form Definition for edit/create this field.
     *
     * @return array Form Definition
     */
    public function getFormDefinition($definition = [])
    {
        return parent::getFormDefinition(ArrayHelper::merge([
            get_class($this) => [
                'type' => 'form',
                'title' => Yii::t('UserModule.profile', 'Select field options'),
                'elements' => [
                    'options' => [
                        'type' => 'textarea',
                        'label' => Yii::t('UserModule.profile', 'Possible values'),
                        'class' => 'form-control autosize',
                        'hint' => Yii::t('UserModule.profile', 'One option per line. Key=>Value Format (e.g. yes=>Yes)'),
                    ],
                ],
            ]], $definition));
    }

    /**
     * Saves this Profile Field Type
     */
    public function save()
    {
        $columnName = $this->profileField->internal_name;
        if (!Profile::columnExists($columnName)) {
            $query = Yii::$app->db->getQueryBuilder()->addColumn(Profile::tableName(), $columnName, 'VARCHAR(255)');
            Yii::$app->db->createCommand($query)->execute();
        }

        return parent::save();
    }

    /**
     * Returns the Field Rules, to validate users input
     *
     * @param array $rules
     * @return array
     */
    public function getFieldRules($rules = [])
    {
        $rules[] = [$this->profileField->internal_name, 'in', 'range' => array_keys($this->getSelectItems())];
        return parent::getFieldRules($rules);
    }

    /**
     * @inheritdoc
     */
    public function getFieldFormDefinition(User $user = null, array $options = []): array
    {
        return parent::getFieldFormDefinition($user, array_merge([
            'items' => $this->getSelectItems(),
            'prompt' => Yii::t('UserModule.profile', 'Please select:'),
        ], $options));
    }

    /**
     * @inheritdoc
     */
    public function getUserValue(User $user, bool $raw = true, bool $encode = true): ?string
    {
        $internalName = $this->profileField->internal_name;
        $value = $user->profile->$internalName ?? '';

        if (!$raw) {
            $options = $this->getSelectItems();
            if (isset($options[$value])) {
                $value = Yii::t($this->profileField->getTranslationCategory(), $options[$value]);
            }
        }

        return $encode ? Html::encode($value) : $value;
    }

}
