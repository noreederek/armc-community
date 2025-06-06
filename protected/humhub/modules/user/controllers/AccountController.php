<?php



namespace humhub\modules\user\controllers;

use Collator;
use Exception;
use humhub\compat\HForm;
use humhub\modules\content\widgets\ContainerTagPicker;
use humhub\modules\space\helpers\MembershipHelper;
use humhub\modules\user\authclient\BaseFormAuth;
use humhub\modules\user\authclient\interfaces\PrimaryClient;
use humhub\modules\user\components\BaseAccountController;
use humhub\modules\user\helpers\AuthHelper;
use humhub\modules\user\models\forms\AccountChangeEmail;
use humhub\modules\user\models\forms\AccountChangeUsername;
use humhub\modules\user\models\forms\AccountDelete;
use humhub\modules\user\models\forms\AccountSettings;
use humhub\modules\user\models\Password;
use humhub\modules\user\models\User;
use humhub\modules\user\Module;
use Throwable;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * AccountController provides all standard actions for the current logged in
 * user account.
 *
 * @author Luke
 * @since 0.5
 */
class AccountController extends BaseAccountController
{
    /**
     * @inheritdoc
     */
    protected $doNotInterceptActionIds = ['delete'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setActionTitles([
            'edit' => Yii::t('UserModule.base', 'Profile'),
            'edit-settings' => Yii::t('UserModule.base', 'Settings'),
            'security' => Yii::t('UserModule.base', 'Security'),
            'connected-accounts' => Yii::t('UserModule.base', 'Connected accounts'),
            'edit-modules' => Yii::t('UserModule.base', 'Modules'),
            'delete' => Yii::t('UserModule.base', 'Delete'),
            'notification' => Yii::t('UserModule.base', 'Notifications'),
            'change-email' => Yii::t('UserModule.base', 'Email'),
            'change-email-validate' => Yii::t('UserModule.base', 'Email'),
            'change-password' => Yii::t('UserModule.base', 'Password'),
        ]);
        return parent::init();
    }

    /**
     * Redirect to current users profile
     * @throws Throwable
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->redirect(Yii::$app->user->getIdentity()->getUrl());
    }

    /**
     * Edit Users Profile
     * @throws Throwable
     */
    public function actionEdit()
    {
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        $user->profile->scenario = 'editProfile';

        // Get Form Definition
        $definition = $user->profile->getFormDefinition();
        $definition['buttons'] = [
            'save' => [
                'type' => 'submit',
                'label' => Yii::t('UserModule.account', 'Save profile'),
                'class' => 'btn btn-primary',
            ],
        ];

        $form = new HForm($definition, $user->profile);
        $form->showErrorSummary = true;
        if ($form->submitted('save') && $form->validate() && $form->save()) {
            // Trigger search refresh
            $user->save();

            $this->view->saved();
            return $this->redirect(['edit']);
        }

        return $this->render('edit', ['hForm' => $form]);
    }

    /**
     * Change Account
     *
     * @todo Add Group
     */
    public function actionEditSettings()
    {
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();

        $model = new AccountSettings();
        $model->language = Yii::$app->i18n->getAllowedLanguage($user->language);
        $model->timeZone = $user->time_zone;
        if (empty($model->timeZone)) {
            $model->timeZone = Yii::$app->settings->get('defaultTimeZone');
        }

        $model->tags = $user->getTags();
        $model->hideOnlineStatus = $user->settings->get('hideOnlineStatus');
        $model->markdownEditorMode = $user->settings->get("markdownEditorMode");
        $model->show_introduction_tour = Yii::$app->getModule('tour')->settings->contentContainer($user)->get("hideTourPanel");
        $model->visibility = $user->visibility;
        $model->blockedUsers = $user->getBlockedUserGuids();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->settings->set('hideOnlineStatus', $model->hideOnlineStatus);
            $user->settings->set('markdownEditorMode', $model->markdownEditorMode);
            Yii::$app->getModule('tour')->settings->contentContainer($user)->set('hideTourPanel', $model->show_introduction_tour);

            $user->scenario = User::SCENARIO_EDIT_ACCOUNT_SETTINGS;
            $user->language = $model->language;
            $user->tagsField = $model->tags;
            $user->time_zone = $model->timeZone;
            $user->visibility = $model->visibility;
            if (Yii::$app->getModule('user')->allowBlockUsers()) {
                $user->blockedUsersField = $model->blockedUsers;
            }
            $user->save();

            $this->view->saved();
            return $this->redirect(['edit-settings']);
        }

        // Sort countries list based on user language
        $languages = Yii::$app->i18n->getAllowedLanguages();
        $col = new Collator(Yii::$app->language);
        $col->asort($languages);

        /* @var $module Module */
        $module = Yii::$app->getModule('user');
        $settingsManager = $module->settings;

        return $this->render('editSettings', [
            'model' => $model,
            'languages' => $languages,
            'isEnabledOnlineStatus' => !$settingsManager->get('auth.hideOnlineStatus'),
        ]);
    }

    /**
     * Returns user tags list in JSON format filtered by keyword
     */
    public function actionSearchTagsJson()
    {
        $keyword = Yii::$app->request->get('keyword');
        $pickerTags = ContainerTagPicker::searchTagsByContainerClass(User::class, $keyword);

        return $this->asJson($pickerTags);
    }

    /**
     * Change Account
     * @throws Exception
     * @todo Add Group
     */
    public function actionPermissions()
    {
        if (empty($this->module->settings->get('enableProfilePermissions'))) {
            throw new HttpException(403);
        }

        $groups = [];
        $groupAccessEnabled = AuthHelper::isGuestAccessEnabled();

        if (Yii::$app->getModule('friendship')->isFriendshipEnabled()) {
            $groups[User::USERGROUP_FRIEND] = Yii::t('UserModule.account', 'Your friends');
            $groups[User::USERGROUP_USER] = Yii::t('UserModule.account', 'Other users');
        } else {
            $groups[User::USERGROUP_USER] = Yii::t('UserModule.account', 'Users');
        }

        if ($groupAccessEnabled) {
            $groups[User::USERGROUP_GUEST] = Yii::t('UserModule.account', 'Not registered users');
        }

        $currentGroup = Yii::$app->request->get('groupId');
        if ($currentGroup == '' || !isset($groups[$currentGroup])) {
            $currentGroup = User::USERGROUP_USER;
        }

        // Handle permission state change
        $return = $this->getUser()->permissionManager->handlePermissionStateChange($currentGroup);

        return $return ?? $this->render('permissions', ['user' => $this->getUser(), 'groups' => $groups, 'group' => $currentGroup, 'multipleGroups' => (count($groups) > 1)]);
    }

    public function actionConnectedAccounts()
    {
        if (Yii::$app->request->isPost && Yii::$app->request->get('disconnect')) {
            foreach (Yii::$app->user->getAuthClientUserService()->getClients() as $authClient) {
                if ($authClient->getId() == Yii::$app->request->get('disconnect')) {
                    Yii::$app->user->getAuthClientUserService()->remove($authClient);
                }
            }
            return $this->redirect(['connected-accounts']);
        }
        $clients = [];
        foreach (Yii::$app->get('authClientCollection')->getClients() as $client) {
            if (!$client instanceof BaseFormAuth && !$client instanceof PrimaryClient) {
                $clients[] = $client;
            }
        }

        $currentAuthProviderId = "";
        if (Yii::$app->user->getCurrentAuthClient() !== null) {
            $currentAuthProviderId = Yii::$app->user->getCurrentAuthClient()->getId();
        }

        $activeAuthClientIds = [];
        foreach (Yii::$app->user->getAuthClientUserService()->getClients() as $authClient) {
            $activeAuthClientIds[] = $authClient->getId();
        }

        return $this->render('connected-accounts', [
            'authClients' => $clients,
            'currentAuthProviderId' => $currentAuthProviderId,
            'activeAuthClientIds' => $activeAuthClientIds,
        ]);
    }

    /**
     * Allows the user to enable user specifc modules
     */
    public function actionEditModules()
    {
        $this->subLayout = '@humhub/modules/user/views/account/_userModulesLayout';

        /* @var User $user */
        $user = Yii::$app->user->getIdentity();

        return $this->render('editModules', [
            'user' => $user,
            'modules' => $user->moduleManager->getAvailable(),
        ]);
    }

    /**
     * @return array|AccountController|\yii\console\Response|Response
     * @throws HttpException
     * @throws Throwable
     */
    public function actionEnableModule()
    {
        $this->forcePostRequest();

        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        $moduleId = Yii::$app->request->get('moduleId');

        $user->moduleManager->enable($moduleId);

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['/user/account/edit-modules']);
        }

        return $this->asJson(['success' => true]);
    }

    /**
     * @return array|AccountController|\yii\console\Response|Response
     * @throws HttpException
     * @throws Throwable
     */
    public function actionDisableModule()
    {
        $this->forcePostRequest();

        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        $moduleId = Yii::$app->request->get('moduleId');

        $user->moduleManager->disable($moduleId);

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['/user/account/edit-modules']);
        } else {
            Yii::$app->response->format = 'json';
            return ['success' => true];
        }
    }

    /**
     * Delete Action
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->getAuthClientUserService()->canDeleteAccount()) {
            throw new HttpException(500, 'Account deletion not allowed!');
        }

        // Ensure user is not owner of a space
        $ownSpaces = MembershipHelper::getOwnSpaces($this->user);
        if (count($ownSpaces) !== 0) {
            return $this->render('delete_spaceowner', ['ownSpaces' => $ownSpaces]);
        }

        $model = new AccountDelete(['user' => $this->getUser()]);
        if ($model->load(Yii::$app->request->post()) && $model->performDelete()) {
            Yii::$app->user->logout();
            return $this->goHome();
        }

        return $this->render('delete', ['model' => $model]);
    }

    /**
     * Change Current Username
     */
    public function actionChangeUsername()
    {
        if (!Yii::$app->user->getAuthClientUserService()->canChangeUsername()) {
            throw new HttpException(500, 'Change Username is not allowed');
        }

        $model = new AccountChangeUsername();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->sendChangeUsername()) {
            return $this->render('changeUsername_success', ['model' => $model]);
        }

        $this->view->warn(Yii::t('UserModule.account', 'Changing the username can make some links unusable, for example old links to the profile.'));
        return $this->render('changeUsername', ['model' => $model]);
    }

    /**
     * Change Current E-mail
     */
    public function actionChangeEmail()
    {
        if (!Yii::$app->user->getAuthClientUserService()->canChangeEmail()) {
            throw new HttpException(500, 'Change E-Mail is not allowed');
        }

        $model = new AccountChangeEmail();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->sendChangeEmail()) {
            return $this->render('changeEmail_success', ['model' => $model]);
        }

        return $this->render('changeEmail', ['model' => $model]);
    }

    /**
     * After the user validated his email
     *
     */
    public function actionChangeEmailValidate()
    {
        if (!Yii::$app->user->getAuthClientUserService()->canChangeEmail()) {
            throw new HttpException(500, 'Change E-Mail is not allowed');
        }

        $token = Yii::$app->request->get('token');
        $email = Yii::$app->request->get('email');

        $user = Yii::$app->user->getIdentity();

        // Check if Token is valid
        if (md5(Yii::$app->settings->get('secret') . $user->guid . $email) != $token) {
            throw new HttpException(404, Yii::t('UserModule.account', 'Invalid link! Please make sure that you entered the entire url.'));
        }

        // Check if E-Mail is in use, e.g. by other user
        $emailAvailablyCheck = User::findOne(['email' => $email]);
        if ($emailAvailablyCheck != null) {
            throw new HttpException(404, Yii::t('UserModule.account', 'The entered e-mail address is already in use by another user.'));
        }

        $user->email = $email;
        $user->save();

        return $this->render('changeEmailValidate', ['newEmail' => $email]);
    }

    /**
     * Change users current password
     */
    public function actionChangePassword()
    {
        if (!Yii::$app->user->getAuthClientUserService()->canChangePassword()) {
            throw new HttpException(500, 'Password change is not allowed');
        }

        $userPassword = new Password();
        $userPassword->scenario = 'changePassword';

        if ($userPassword->load(Yii::$app->request->post()) && $userPassword->validate()) {
            $userPassword->user_id = Yii::$app->user->id;
            $userPassword->setPassword($userPassword->newPassword);
            if ($userPassword->save()) {
                return $this->render('changePassword_success');
            }
        }

        return $this->render('changePassword', ['model' => $userPassword]);
    }

    /**
     * Crops the banner image of the user
     * @deprecated since version 1.2
     */
    public function actionCropBannerImage()
    {
        return Yii::$app->runAction('/user/image/crop', ['type' => ImageController::TYPE_PROFILE_BANNER_IMAGE]);
    }

    /**
     * Handle the banner image upload
     *
     * @deprecated since version 1.2
     */
    public function actionBannerImageUpload()
    {
        // Ensure view file backward compatibility prior 1.2
        if (isset($_FILES['bannerfiles'])) {
            $_FILES['images'] = $_FILES['bannerfiles'];
        }
        return Yii::$app->runAction('/user/image/upload', ['type' => ImageController::TYPE_PROFILE_BANNER_IMAGE]);
    }

    /**
     * Handle the profile image upload
     *
     * @deprecated since version 1.2
     */
    public function actionProfileImageUpload()
    {
        // Ensure view file backward compatibility prior 1.2
        if (isset($_FILES['profilefiles'])) {
            $_FILES['images'] = $_FILES['profilefiles'];
        }
        return Yii::$app->runAction('/user/image/upload', ['type' => ImageController::TYPE_PROFILE_IMAGE]);
    }

    /**
     * Crops the profile image of the user
     * @deprecated since version 1.2
     */
    public function actionCropProfileImage()
    {
        return Yii::$app->runAction('/user/image/crop', ['type' => ImageController::TYPE_PROFILE_IMAGE]);
    }

    /**
     * Deletes the profile image or profile banner
     * @deprecated since version 1.2
     */
    public function actionDeleteProfileImage()
    {
        return Yii::$app->runAction('/user/image/delete', ['type' => (Yii::$app->request->get('type', 'profile') == 'profile') ? ImageController::TYPE_PROFILE_IMAGE : ImageController::TYPE_PROFILE_BANNER_IMAGE]);
    }

    /**
     * Returns the current user of this account
     *
     * An administration can also pass a user id via GET parameter to change users
     * accounts settings.
     *
     * @return User the user
     * @throws HttpException
     * @throws Throwable
     */
    public function getUser()
    {
        if (Yii::$app->request->get('userGuid') != '' && Yii::$app->user->getIdentity()->isSystemAdmin()) {
            $user = User::findOne(['guid' => Yii::$app->request->get('userGuid')]);
            if ($user === null) {
                throw new HttpException(404, 'Could not find user!');
            }
            return $user;
        }

        return Yii::$app->user->getIdentity();
    }
}
