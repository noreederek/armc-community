<?php



namespace humhub\modules\admin\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\admin\permissions\ManageSettings;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\content\components\ContentContainerDefaultPermissionManager;
use humhub\modules\content\models\ContentContainerPermission;
use humhub\modules\user\models\User;
use humhub\modules\user\Module;
use Yii;
use yii\web\HttpException;

/**
 * User default permissions management
 *
 * @since 1.8
 */
class UserPermissionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public $adminOnly = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->appendPageTitle(Yii::t('AdminModule.base', 'Users'));
        $this->subLayout = '@admin/views/layouts/user';
    }

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            ['permissions' => [ManageUsers::class]],
            ['permissions' => [ManageSettings::class], 'actions' => ['index']],
        ];
    }

    /**
     * Default User Permissions
     */
    public function actionIndex()
    {
        $defaultPermissionManager = new ContentContainerDefaultPermissionManager([
            'contentContainerClass' => User::class,
        ]);

        $groups = User::getUserGroups();

        $groupId = Yii::$app->request->get('groupId', User::USERGROUP_USER);
        if (!array_key_exists($groupId, $groups)) {
            throw new HttpException(500, 'Invalid group id given!');
        }

        // Handle permission state change
        $return = $defaultPermissionManager->handlePermissionStateChange($groupId);

        return $return ?? $this->render('default', [
            'defaultPermissionManager' => $defaultPermissionManager,
            'groups' => $groups,
            'groupId' => $groupId,
        ]);
    }

    public function actionSwitchIndividualProfilePermissions()
    {
        $this->forcePostRequest();

        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');
        $oldState = (bool)$userModule->settings->get('enableProfilePermissions', false);
        $newState = false;
        if (Yii::$app->request->post('isEnabled') === 'true') {
            $newState = true;
        }

        if ($oldState === true && $newState === false) {
            ContentContainerPermission::deleteAll('contentcontainer_id IN (SELECT contentcontainer_id FROM user)');
            $userModule->settings->set('enableProfilePermissions', false);
        } elseif ($oldState === false && $newState === true) {
            $userModule->settings->set('enableProfilePermissions', true);
        }

        return $this->asJson(['ok' => true, 'oldState' => $oldState, 'newState' => $newState]);
    }

}
