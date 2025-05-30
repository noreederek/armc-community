<?php



namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\Module;
use humhub\modules\user\services\AuthClientUserService;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "profile".
 *
 * @property int $user_id
 * @property string $firstname
 * @property string $lastname
 * @property string $title
 * @property string $gender
 * @property string $street
 * @property string $zip
 * @property string $city
 * @property string $country
 * @property string $state
 * @property int $birthday_hide_year
 * @property string $birthday
 * @property string $about
 * @property string $phone_private
 * @property string $phone_work
 * @property string $mobile
 * @property string $fax
 * @property string $im_skype
 * @property string $im_msn
 * @property int $im_icq
 * @property string $im_xmpp
 * @property string $url
 * @property string $url_facebook
 * @property string $url_linkedin
 * @property string $url_xing
 * @property string $url_youtube
 * @property string $url_vimeo
 * @property string $url_flickr
 * @property string $url_myspace
 * @property string $url_googleplus
 * @property string $url_twitter
 * @property User $user
 */
class Profile extends ActiveRecord
{
    /**
     * @since 1.3.2
     */
    public const SCENARIO_EDIT_ADMIN = 'editAdmin';
    public const SCENARIO_REGISTRATION = 'registration';
    public const SCENARIO_EDIT_PROFILE = 'editProfile';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['firstname', 'lastname'], 'trim'],
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
        ];

        foreach (static::getValidProfileFields(true) as $profileField) {
            $rules = array_merge($rules, $profileField->getFieldType()->getFieldRules());
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        static $scenarios;

        if (!empty($scenarios)) {
            return $scenarios;
        }

        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT_ADMIN] = [];
        $scenarios[self::SCENARIO_REGISTRATION] = [];
        $scenarios[self::SCENARIO_EDIT_PROFILE] = [];

        // Get synced attributes if user is set
        $syncAttributes = [];
        if ($this->user !== null) {
            $syncAttributes = (new AuthClientUserService($this->user))->getSyncAttributes();
        }

        foreach (static::getValidProfileFields() as $profileField) {
            // Some fields consist of multiple field definitions (e.g. Birthday)
            foreach ($profileField->fieldType->getFieldFormDefinition($this->user) as $fieldName => $definition) {
                // Skip automatically synced attributes (readonly)
                if (in_array($profileField->internal_name, $syncAttributes)) {
                    continue;
                }

                $scenarios[self::SCENARIO_EDIT_ADMIN][] = $fieldName;

                if ($profileField->editable && !in_array($profileField->internal_name, $syncAttributes)) {
                    $scenarios[self::SCENARIO_EDIT_PROFILE][] = $fieldName;
                }

                if ($profileField->show_at_registration) {
                    $scenarios[self::SCENARIO_REGISTRATION][] = $fieldName;
                }
            }
        }

        return $scenarios;
    }

    /**
     * Internal
     *
     * Just holds message labels for the Yii Message Command
     */
    private function translationOnly()
    {
        Yii::t('UserModule.profile', 'First name');
        Yii::t('UserModule.profile', 'Last name');
        Yii::t('UserModule.profile', 'Title');
        Yii::t('UserModule.profile', 'Street');
        Yii::t('UserModule.profile', 'Zip');
        Yii::t('UserModule.profile', 'City');
        Yii::t('UserModule.profile', 'Country');
        Yii::t('UserModule.profile', 'State');
        Yii::t('UserModule.profile', 'About');
        Yii::t('UserModule.profile', 'Birthday');
        Yii::t('UserModule.profile', 'Hide year in profile');

        Yii::t('UserModule.profile', 'Gender');
        Yii::t('UserModule.profile', 'Male');
        Yii::t('UserModule.profile', 'Female');
        Yii::t('UserModule.profile', 'Diverse');
        Yii::t('UserModule.profile', 'Hide year in profile');

        Yii::t('UserModule.profile', 'Phone Private');
        Yii::t('UserModule.profile', 'Phone Work');
        Yii::t('UserModule.profile', 'Mobile');
        Yii::t('UserModule.profile', 'E-Mail');
        Yii::t('UserModule.profile', 'Fax');
        Yii::t('UserModule.profile', 'XMPP Jabber Address');

        Yii::t('UserModule.profile', 'Website URL');
        Yii::t('UserModule.profile', 'Facebook URL');
        Yii::t('UserModule.profile', 'LinkedIn URL');
        Yii::t('UserModule.profile', 'Xing URL');
        Yii::t('UserModule.profile', 'YouTube URL');
        Yii::t('UserModule.profile', 'Vimeo URL');
        Yii::t('UserModule.profile', 'TikTok URL');
        Yii::t('UserModule.profile', 'Instagram URL');
        Yii::t('UserModule.profile', 'Twitter URL');
        Yii::t('UserModule.profile', 'Mastodon URL');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        static $labels;

        if (!empty($labels)) {
            return $labels;
        }

        $labels = [];
        foreach (static::getValidProfileFields() as $profileField) {
            /** @var ProfileField $profileField */
            $labels = array_merge($labels, $profileField->getFieldType()->getLabels());
        }

        return $labels;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Returns the Profile as CForm
     */
    public function getFormDefinition()
    {
        $definition = [];
        $definition['elements'] = [];

        $syncAttributes = [];
        if ($this->user !== null) {
            $syncAttributes = (new AuthClientUserService($this->user))->getSyncAttributes();
        }

        $safeAttributes = $this->safeAttributes();

        foreach (ProfileFieldCategory::find()->orderBy('sort_order')->all() as $profileFieldCategory) {
            $category = [
                'type' => 'form',
                'title' => Yii::t($profileFieldCategory->getTranslationCategory(), $profileFieldCategory->title),
                'elements' => [],
            ];

            foreach (
                ProfileField::find()->orderBy('sort_order')
                    ->where(['profile_field_category_id' => $profileFieldCategory->id])->all() as $profileField
            ) {
                /** @var ProfileField $profileField */
                $profileField->editable = true;

                if ($profileField->fieldType->isVirtual) {
                    continue;
                }

                if (!in_array($profileField->internal_name, $safeAttributes)) {
                    if ($profileField->visible && $this->scenario != 'registration') {
                        $profileField->editable = false;
                    } else {
                        continue;
                    }
                }

                // Dont allow editing of ldap syned fields - will be overwritten on next ldap sync.
                if (in_array($profileField->internal_name, $syncAttributes)) {
                    $profileField->editable = false;
                }

                $fieldDefinition = $profileField->fieldType->getFieldFormDefinition($this->user);

                if (isset($fieldDefinition[$profileField->internal_name]) && !empty($profileField->description)) {
                    $fieldDefinition[$profileField->internal_name]['hint'] = Yii::t($profileField->getTranslationCategory() ?: $profileFieldCategory->getTranslationCategory(), $profileField->description);
                }

                $category['elements'] = array_merge($category['elements'], $fieldDefinition);

                $profileField->fieldType->loadDefaults($this);
            }

            $definition['elements']['category_' . $profileFieldCategory->id] = $category;
        }

        return $definition;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        foreach (static::getValidProfileFields(true) as $profileField) {
            $key = $profileField->internal_name;
            $this->$key = $profileField->getFieldType()->beforeProfileSave($this->$key);
        }

        return parent::beforeSave($insert);
    }

    /**
     * Checks if the given column name already exists on the profile table.
     *
     * @param String $name
     * @return Boolean
     */
    public static function columnExists($name)
    {
        Yii::$app->getDb()->getSchema()->refreshTableSchema(self::tableName());
        $table = Yii::$app->getDb()->getSchema()->getTableSchema(self::tableName(), true);
        $columnNames = $table->getColumnNames();

        return (in_array($name, $columnNames));
    }

    /**
     * Returns all profile field categories with some user data
     *
     * @return ProfileFieldCategory[]
     */
    public function getProfileFieldCategories()
    {
        $categories = [];

        foreach (ProfileFieldCategory::find()->orderBy('sort_order')->all() as $category) {
            if (count($this->getProfileFields($category)) > 0) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * @return ProfileField[] all profile fields with user data by given category
     */
    public function getProfileFields(?ProfileFieldCategory $category = null, ?array $withoutTypes = null): array
    {
        $fields = [];

        if ($this->user !== null) {
            $query = ProfileField::find()
                ->where(['visible' => 1])
                ->andFilterWhere(['NOT IN', 'field_type_class', (array) $withoutTypes])
                ->orderBy('sort_order');

            if ($category !== null) {
                $query->andWhere(['profile_field_category_id' => $category->id]);
            }

            /** @var ProfileField $profileFieldModels */
            $profileFieldModels = $query->all();

            foreach ($profileFieldModels as $field) {
                if (!empty($field->getUserValue($this->user))) {
                    $fields[] = $field;
                }
            }
        }

        return $fields;
    }

    /**
     * Returns unsorted, unfiltered list of ProfileFields, skipping those without a valid field type
     *
     * @param bool $skipVirtual if provided and true, virtual fields will be omitted
     *
     * @return array
     * @since 1.15
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected static function &getValidProfileFields(bool $skipVirtual = false): array
    {
        $result = [];

        foreach (ProfileField::find()->all() as $profileField) {
            try {
                $fieldType = $profileField->getFieldType();
            } catch (Exception $e) {
                if (YII_DEBUG) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    throw $e;
                }

                Yii::error($e->getMessage());

                continue;
            }

            if ($fieldType === null) {
                $message = sprintf("Field %s has no valid type associated", $profileField->internal_name);

                if (YII_DEBUG) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    throw new \Exception($message);
                }

                Yii::error($message);

                continue;
            }

            if ($skipVirtual && $fieldType->isVirtual) {
                continue;
            }

            $result[] = $profileField;
        }

        return $result;
    }

    /**
     * Soft delete will empty all profile fields except these defined in the module configuration.
     */
    public function softDelete()
    {
        $module = Yii::$app->getModule('user');
        /* @var $module Module */

        foreach (array_keys($this->getAttributes()) as $name) {
            if (!in_array($name, $module->softDeleteKeepProfileFields) && $name !== 'user_id') {
                $this->setAttribute($name, '');
            }
        }

        if (!$this->save(false)) {
            Yii::error('Could not soft delete profile!');
        }
    }

    /**
     * Get field value for this profile
     *
     * @param string $field
     * @param bool $raw
     * @param bool $encode
     * @return string|null
     */
    public function getFieldValue(string $field, bool $raw = false, bool $encode = true): ?string
    {
        if (!$this->hasAttribute($field) || !$this->user) {
            return null;
        }

        $profileField = ProfileField::findOne(['internal_name' => $field]);

        return $profileField?->getUserValue($this->user, $raw, $encode);
    }
}
