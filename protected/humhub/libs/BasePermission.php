<?php


namespace humhub\libs;

use humhub\modules\space\models\Space;
use humhub\modules\user\components\PermissionManager;
use humhub\modules\user\models\User;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;
use yii\base\Exception;

/**
 * BasePermission

 * @author luke
 */
class BasePermission extends BaseObject
{
    /**
     * @event Event an event that is triggered when the permission is initialized via [[init()]].
     */
    public const EVENT_INIT = 'init';

    /**
     * Permission States
     */
    public const STATE_DEFAULT = '';
    public const STATE_ALLOW = 1;
    public const STATE_DENY = 0;

    /**
     * @var string id of the permission (default is classname)
     */
    protected $id;

    /**
     * @var string title of the permission
     */
    protected $title = '';

    /**
     * @var string description of the permission
     */
    protected $description = '';

    /**
     * @var string module id which belongs to the permission
     */
    protected $moduleId = '';

    /**
     * A list of groupIds which allowed per default.
     *
     * @var array default allowed groups
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
    ];

    /**
     * A list of groupIds which are fixed group state.
     * See defaultState for default setting.
     *
     * @var array default fixed groups
     */
    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
    ];

    /**
     * The default state of this permission
     *
     * @var string
     */
    protected $defaultState = self::STATE_DENY;

    /**
     * Optional contentContainer instance to improve title and description.
     *
     * @since 1.2
     * @var \humhub\modules\content\components\ContentContainerActiveRecord
     */
    public $contentContainer = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Event::trigger($this, self::EVENT_INIT);
    }

    /**
     * Returns the ID
     *
     * @return string the id of the permission
     */
    public function getId()
    {
        if ($this->id != '') {
            return $this->id;
        }

        return static::class;
    }

    /**
     * Returns the title
     *
     * @return string the title of the permission
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the description
     *
     * @return string the description of the permission
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the module id
     *
     * @return string the moduleid of the permission
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * This function must return a unique que.
     * It must either start with the module id, or be a valid UUID.
     * The function may return null to avoid caching in the PermissionManager.
     *
     * @param array $parameters Parameters passed to PermissionManager::can() which allows a permission to evaluate if caching should be disabled
     *
     * @return string|null A unique string (see method description) or null, if caching should be disabled
     * @since 1.15.2
     * @see PermissionManager::can()
     * @see UUID::v4()
     * @noinspection PhpUnusedParameterInspection
     */
    public function getCacheKey(array $parameters = []): ?string
    {
        return $this->getModuleId() . $this->getId();
    }

    /**
     * Returns the default state of the permission.
     * The defaultState is either defined by setting $defaultState attribute
     * or by overwriting the $defaultState by means of the configuration param 'defaultPermissions'.
     *
     * If the $defaultState is set to denied, we can grant the permission for specific groups by defining
     * the $defaultAllowedGroups array.
     *
     * @return int the default state
     */
    public function getDefaultState($groupId)
    {
        $configuredState = $this->getConfiguredState($groupId);
        if ($configuredState !== null) {
            return $configuredState;
        }

        if ($this->defaultState == self::STATE_ALLOW) {
            return self::STATE_ALLOW;
        }

        return (int) (in_array($groupId, $this->defaultAllowedGroups));
    }

    /**
     * Returns the default state set in the configration params 'defaultPermissions'.
     * This method returns null in case the default state for this permission or group is not set in
     * the configuration.
     *
     * @param int $groupId
     * @return int|null
     * @since 1.2
     */
    protected function getConfiguredState($groupId)
    {
        if (!isset(Yii::$app->params['defaultPermissions'][static::class])) {
            return null;
        }

        if (isset(Yii::$app->params['defaultPermissions'][static::class][$groupId])) {
            return Yii::$app->params['defaultPermissions'][static::class][$groupId];
        }

        // Allow asterisk to overwrite all groups excluding guest groups
        if (
            isset(Yii::$app->params['defaultPermissions'][static::class]['*'])
            && !in_array($groupId, [Space::USERGROUP_GUEST, User::USERGROUP_GUEST], true)
        ) {
            return Yii::$app->params['defaultPermissions'][static::class]['*'];
        }

        return null;
    }

    /**
     * Checks if permission state can be changed
     *
     * @return bool
     */
    public function canChangeState($groupId)
    {
        return (!in_array($groupId, $this->fixedGroups));
    }

    /**
     * Checks the given id belongs to this permission
     *
     * @return bool
     */
    public function hasId($id)
    {
        return ($this->getId() == $id);
    }

    /**
     * Returns the label for given State
     *
     * @return string the label
     * @throws Exception
     */
    public static function getLabelForState($state)
    {
        if ($state === self::STATE_ALLOW) {
            return Yii::t('base', 'Allow');
        } elseif ($state === self::STATE_DENY) {
            return Yii::t('base', 'Deny');
        } elseif ($state == '') {
            return Yii::t('base', 'Default');
        }

        throw new Exception('Invalid permission state');
    }

    /**
     * @param array Ids of additional fixed groups
     */
    public function addFixedGroups($groupIds)
    {
        if (is_array($groupIds) && !empty($groupIds)) {
            $this->fixedGroups = array_merge($this->fixedGroups, $groupIds);
        }
    }
}
