<?php



namespace humhub\modules\user\models\fieldtype;

use humhub\modules\user\models\Profile;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\Html;

/**
 * ProfileFieldTypeText handles text profile fields.
 *
 * @package humhub.modules_core.user.models
 * @since 0.5
 */
class Text extends BaseType
{
    public const VALIDATOR_EMAIL = "email";
    public const VALIDATOR_URL = "url";

    /**
     * Minimum Text Length
     *
     * @var Integer
     */
    public $minLength;

    /**
     * Maximum Text Length
     *
     * @var Integer
     */
    public $maxLength = 255;

    /**
     * Validator to use (email, url, none)
     *
     * @var String
     */
    public $validator;

    /**
     * Field Default Text
     *
     * @var String
     */
    public $default;

    /**
     * Regular Expression to check the field
     *
     * @var String
     */
    public $regexp;

    /**
     * Error Message when regular expression fails
     *
     * @var String
     */
    public $regexpErrorMessage;

    /**
     * @inerhitdoc
     */
    public $canBeDirectoryFilter = true;

    /**
     * LinkPrefix - tel://, sip://, xmpp:// etc
     *
     * @since 1.11
     * @var string
     */
    public $linkPrefix;

    /**
     * Rules for validating the Field Type Settings Form
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['default', 'minLength', 'maxLength', 'validator', 'regexp', 'regexpErrorMessage'], 'safe'],
            [['maxLength', 'minLength'], 'integer', 'min' => 1, 'max' => 255],
            [['default'], 'string', 'max' => 255],
            [['linkPrefix'], 'string', 'max' => 10],
        ];
    }

    /**
     * Returns Form Definition for edit/create this field.
     *
     * @param array $definition
     * @return array Form Definition
     */
    public function getFormDefinition($definition = [])
    {
        return parent::getFormDefinition([
            get_class($this) => [
                'type' => 'form',
                'title' => Yii::t('UserModule.profile', 'Text Field Options'),
                'elements' => [
                    'validator' => [
                        'label' => Yii::t('UserModule.profile', 'Validator'),
                        'type' => 'dropdownlist',
                        'class' => 'form-control',
                        'items' => [
                            '' => 'None',
                            self::VALIDATOR_EMAIL => 'E-Mail Address',
                            self::VALIDATOR_URL => 'URL',
                        ],
                    ],
                    'linkPrefix' => [
                        'label' => Yii::t('UserModule.profile', 'Link Prefix (e.g. https://, mailto:, tel://)'),
                        'type' => 'text',
                        'class' => 'form-control',
                    ],
                    'minLength' => [
                        'label' => Yii::t('UserModule.profile', 'Minimum length'),
                        'type' => 'text',
                        'class' => 'form-control',
                    ],
                    'maxLength' => [
                        'label' => Yii::t('UserModule.profile', 'Maximum length'),
                        'class' => 'form-control',
                        'type' => 'text',
                    ],
                    'default' => [
                        'label' => Yii::t('UserModule.profile', 'Default value'),
                        'class' => 'form-control',
                        'type' => 'text',
                    ],
                    'regexp' => [
                        'label' => Yii::t('UserModule.profile', 'Regular Expression: Validator'),
                        'class' => 'form-control',
                        'type' => 'text',
                    ],
                    'regexpErrorMessage' => [
                        'label' => Yii::t('UserModule.profile', 'Regular Expression: Error message'),
                        'class' => 'form-control',
                        'type' => 'text',
                    ],
                ],
            ]]);
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

        if ($this->validator == self::VALIDATOR_EMAIL) {
            $rules[] = [$this->profileField->internal_name, 'email'];
        } elseif ($this->validator == self::VALIDATOR_URL) {
            $rules[] = [$this->profileField->internal_name, 'url'];
        }

        if ($this->maxLength == "" || $this->maxLength > 255) {
            $rules[] = [$this->profileField->internal_name, 'string', 'max' => 255];
        } else {
            $rules[] = [$this->profileField->internal_name, 'string', 'max' => $this->maxLength];
        }

        if ($this->minLength != "") {
            $rules[] = [$this->profileField->internal_name, 'string', 'min' => $this->minLength];
        }

        if ($this->regexp != "") {
            $errorMsg = $this->regexpErrorMessage;
            if (empty($errorMsg)) {
                $errorMsg = Yii::t('UserModule.profile', "Invalid!");
            } else {
                $errorMsg = Yii::t($this->profileField->getTranslationCategory(), $errorMsg);
            }

            $rules[] = [$this->profileField->internal_name, 'match', 'pattern' => $this->regexp, 'message' => $errorMsg];
        }

        return parent::getFieldRules($rules);
    }

    /**
     * @inheritdoc
     */
    public function getUserValue(User $user, bool $raw = true, bool $encode = true): ?string
    {
        $internalName = $this->profileField->internal_name;
        $value = $user->profile->$internalName ?? '';

        if (!$raw && (in_array($this->validator, [self::VALIDATOR_EMAIL, self::VALIDATOR_URL]) || !empty($this->linkPrefix))) {
            $linkPrefix = ($this->validator === self::VALIDATOR_EMAIL) ? 'mailto:' : $this->linkPrefix;
            return Html::a($encode ? Html::encode($value) : $value, $linkPrefix . $value);
        }

        return $encode ? Html::encode($value) : $value;
    }

}
