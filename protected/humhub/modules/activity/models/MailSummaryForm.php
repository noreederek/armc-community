<?php



namespace humhub\modules\activity\models;

use humhub\modules\activity\components\MailSummary;
use humhub\modules\activity\Module;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\content\models\ContentContainerSetting;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * MailSummaryForm
 *
 * @since 1.2
 * @author Luke
 */
class MailSummaryForm extends Model
{
    /**
     * Space limit modes (include or exclude)
     */
    public const LIMIT_MODE_EXCLUDE = 0;
    public const LIMIT_MODE_INCLUDE = 1;

    /**
     * @var array of selected activities to include
     */
    public $activities = [];

    /**
     * @var int the mail summary interval
     */
    public $interval;

    /**
     * @var array the selected spaces
     */
    public $limitSpaces;

    /**
     * @var int the mode how to handle selected spaces (include or exclude)
     */
    public $limitSpacesMode = 0;

    /**
     * @var User the user when user settings should be loaded/saved
     */
    public $user;

    /**
     * @var bool indicates that custom user settings were loaded
     */
    public $userSettingsLoaded = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interval'], 'integer'],
            [['activities'], 'in', 'range' => array_keys($this->getActivitiesArray())],
            [['limitSpaces'], 'safe'],
            [['limitSpacesMode'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'interval' => Yii::t('ActivityModule.base', 'Interval'),
            'limitSpacesMode' => Yii::t('ActivityModule.base', 'Spaces'),
            'activities' => Yii::t('ActivityModule.base', 'Activities'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'interval' => Yii::t('ActivityModule.base', 'You will only receive an e-mail if there is something new.'),
        ];
    }

    /**
     * Returns available modes how to handle given spaces
     *
     * @return array the modes
     */
    public function getLimitSpaceModes()
    {
        return [
            static::LIMIT_MODE_EXCLUDE => Yii::t('ActivityModule.base', 'Exclude spaces below from the mail summary'),
            static::LIMIT_MODE_INCLUDE => Yii::t('ActivityModule.base', 'Only include spaces below to the mail summary'),
        ];
    }

    /**
     * Returns a list of available mail summary intervals
     *
     * @return array the intervals
     */
    public function getIntervals()
    {
        return [
            MailSummary::INTERVAL_NONE => Yii::t('ActivityModule.base', 'Never'),
            MailSummary::INTERVAL_HOURLY => Yii::t('ActivityModule.base', 'Hourly'),
            MailSummary::INTERVAL_DAILY => Yii::t('ActivityModule.base', 'Daily'),
            MailSummary::INTERVAL_WEEKLY => Yii::t('ActivityModule.base', 'Weekly'),
            MailSummary::INTERVAL_MONTHLY => Yii::t('ActivityModule.base', 'Monthly'),
        ];
    }

    /**
     * Returns an array of all possible activities for the checkboxLis
     *
     * @return array
     */
    public function getActivitiesArray()
    {
        $contents = [];

        foreach (Module::getConfigurableActivities() as $activity) {
            $contents[get_class($activity)] = $activity->getTitle() . ' - ' . $activity->getDescription();
        }

        return $contents;
    }

    /**
     * Loads the current values into this model
     *
     * If the 'user' attribute is set, the user settings are loaded if present.
     * Otherwise the system defaults will be loaded.
     *
     * @return bool
     */
    public function loadCurrent()
    {
        // Only load user settings when user is given and the user has own settings
        if ($this->user !== null && Yii::$app->getModule('activity')->settings->user($this->user)->get('mailSummaryInterval') !== null) {
            $settingsManager = Yii::$app->getModule('activity')->settings->user($this->user);
            $this->userSettingsLoaded = true;
        } else {
            $settingsManager = Yii::$app->getModule('activity')->settings;
        }

        $this->interval = $settingsManager->get('mailSummaryInterval');
        $this->limitSpacesMode = $settingsManager->get('mailSummaryLimitSpacesMode');
        $mailSummaryLimitSpaces = $settingsManager->get('mailSummaryLimitSpaces');
        $this->limitSpaces = (!empty($mailSummaryLimitSpaces)) ? explode(',', $mailSummaryLimitSpaces) : [];

        // Since we store only disabled activities, we need to enable the difference
        $mailSummaryActivitySuppress = $settingsManager->get('mailSummaryActivitySuppress');
        $suppressedActivities = (!empty($mailSummaryActivitySuppress)) ? explode(',', $mailSummaryActivitySuppress) : [];
        $this->activities = array_diff(array_keys($this->getActivitiesArray()), $suppressedActivities);

        return true;
    }

    /**
     * Saves the current model values to the current user or globally.
     *
     * @return bool success
     */
    public function save()
    {
        if ($this->user !== null) {
            $settingsManager = Yii::$app->getModule('activity')->settings->user($this->user);
            $this->userSettingsLoaded = true;
        } else {
            $settingsManager = Yii::$app->getModule('activity')->settings;
        }

        if (!is_array($this->activities)) {
            $this->activities = [];
        }
        if (!is_array($this->limitSpaces)) {
            $this->limitSpaces = [];
        }

        $settingsManager->set('mailSummaryInterval', $this->interval);
        $settingsManager->set('mailSummaryLimitSpaces', implode(',', $this->limitSpaces));
        $settingsManager->set('mailSummaryLimitSpacesMode', $this->limitSpacesMode);

        // We got a list of enabled activities, but we store only disabled activity class names
        $disabledActivities = array_diff(array_keys($this->getActivitiesArray()), $this->activities);
        $settingsManager->set('mailSummaryActivitySuppress', implode(',', $disabledActivities));

        return true;
    }

    /**
     * @return string[]
     */
    public static function getUserSettingNames()
    {
        return [
            'mailSummaryInterval',
            'mailSummaryLimitSpaces',
            'mailSummaryLimitSpacesMode',
            'mailSummaryActivitySuppress',
        ];
    }

    /**
     * Resets all settings stored for the current user
     *
     * @throws Exception
     */
    public function resetUserSettings()
    {
        if ($this->user === null) {
            throw new Exception('Could not reset settings when no user is set!');
        }

        $settingsManager = static::getModule()->settings->user($this->user);
        foreach (static::getUserSettingNames() as $userSettingName) {
            $settingsManager->delete($userSettingName);
        }
    }

    /**
     * @return Module
     */
    private static function getModule()
    {
        return Yii::$app->getModule('activity');
    }

    /**
     * @return bool
     */
    public function canResetAllUsers()
    {
        return !isset($this->user) && Yii::$app->user->can(ManageUsers::class);
    }

    /**
     * Resets all settings stored for all current user
     */
    public function resetAllUserSettings()
    {
        ContentContainerSetting::deleteAll(['AND',
            ['module_id' => static::getModule()->id],
            ['IN', 'name', static::getUserSettingNames()],
        ]);

        $settingsManager = static::getModule()->settings->user();
        $settingsManager->reload();
    }

}
