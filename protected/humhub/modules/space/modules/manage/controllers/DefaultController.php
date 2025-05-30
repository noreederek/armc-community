<?php


namespace humhub\modules\space\modules\manage\controllers;

use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\space\components\UrlRule;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Menu;
use humhub\modules\space\widgets\Chooser;
use humhub\modules\space\modules\manage\components\Controller;
use humhub\modules\space\modules\manage\models\DeleteForm;
use humhub\modules\space\activities\SpaceArchived;
use humhub\modules\space\activities\SpaceUnArchived;
use Yii;
use yii\helpers\Url;

/**
 * Default space admin action
 *
 * @author Luke
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            ['login'],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_ADMIN], 'actions' => ['index', 'advanced']],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_OWNER], 'actions' => ['archive', 'unarchive', 'delete']],
            [ContentContainerControllerAccess::RULE_POST => ['archive', 'unarchive']],
        ];
    }

    /**
     * General space settings
     */
    public function actionIndex()
    {
        $space = $this->contentContainer;
        $space->scenario = 'edit';
        $space->blockedUsersField = $space->getBlockedUserGuids();

        if ($space->load(Yii::$app->request->post()) && $space->validate() && $space->save()) {
            RichText::postProcess($space->about, $space);
            $this->view->saved();
            return $this->redirect($space->createUrl('index'));
        }

        return $this->render('index', ['model' => $space]);
    }

    public function actionAdvanced()
    {
        $model = $this->space->getAdvancedSettings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            unset(UrlRule::$containerUrlMap[$this->contentContainer->guid]);
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('advanced'));
        }

        $indexModuleSelection = Menu::getAvailablePages();
        unset($indexModuleSelection[Url::to(['/space/space/home', 'container' => $this->contentContainer])]);

        // To avoid infinite redirects of actionIndex we remove the stream value and set an empty selection instead
        $indexModuleSelection = ['' => Yii::t('SpaceModule.manage', 'Stream (Default)')] + $indexModuleSelection;

        return $this->render('advanced', [
            'model' => $model,
            'space' => $this->contentContainer,
            'indexModuleSelection' => $indexModuleSelection,
        ]);
    }

    /**
     * Archives the space
     */
    public function actionArchive()
    {
        $space = $this->getSpace();
        $space->archive();

        // Create Activity when the space in archived
        SpaceArchived::instance()->from(Yii::$app->user->getIdentity())->about($space->owner)->save();

        return $this->asJson([
            'success' => true,
            'space' => Chooser::getSpaceResult($space, true, ['isMember' => true]),
        ]);
    }

    /**
     * Unarchives the space
     */
    public function actionUnarchive()
    {
        $space = $this->getSpace();
        $space->unarchive();

        // Create Activity when the space in unarchieved
        SpaceUnArchived::instance()->from(Yii::$app->user->getIdentity())->about($space->owner)->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';
            return [
                'success' => true,
                'space' => Chooser::getSpaceResult($space, true, ['isMember' => true]),
            ];
        }

        return $this->redirect($space->createUrl('/space/manage'));
    }

    /**
     * Deletes the space
     */
    public function actionDelete()
    {
        $model = new DeleteForm();
        $model->spaceName = $this->getSpace()->name;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->getSpace()->delete();
            return $this->goHome();
        }

        return $this->render('delete', ['model' => $model, 'space' => $this->getSpace()]);
    }
}
