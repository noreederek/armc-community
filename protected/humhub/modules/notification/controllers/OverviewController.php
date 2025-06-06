<?php



namespace humhub\modules\notification\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\models\forms\FilterForm;
use humhub\modules\notification\models\Notification;
use humhub\modules\notification\widgets\OverviewWidget;
use Throwable;
use Yii;
use yii\db\IntegrityException;
use yii\db\StaleObjectException;

/**
 * ListController
 *
 * @package humhub.modules_core.notification.controllers
 * @since 0.5
 */
class OverviewController extends Controller
{
    public const PAGINATION_PAGE_SIZE = 20;

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY],
        ];
    }

    /**
     * @param bool $reload if the request is a reload request
     * @return string
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionIndex($reload = false)
    {
        $filterForm = $this->loadFilterForm($reload);

        if ($filterForm->hasFilter()) {
            $overview = OverviewWidget::widget([
                'pagination' => $filterForm->getPagination(self::PAGINATION_PAGE_SIZE),
                'notifications' => $this->prepareNotifications($filterForm->createQuery()->all()),
            ]);
        } else {
            $overview = OverviewWidget::widget([
                'notifications' => [],
            ]);
        }

        return $reload
            ? $this->renderAjaxPartial($overview)
            : $this->render('index', [
                'overview' => $overview,
                'filterForm' => $filterForm,
            ]);
    }

    /**
     * Loads the filters from the request into the form
     *
     * @param bool $reload
     * @return FilterForm
     */
    private function loadFilterForm(bool $reload = false): FilterForm
    {
        $filterForm = new FilterForm();

        if ($reload) {
            $filterForm->load(Yii::$app->request->post());
        } else {
            $filterForm->load(Yii::$app->request->get());
        }

        return $filterForm;
    }

    /**
     * Validates given notifications and returns a list of notification models of all valid notifications.
     *
     * @param $notifications Notification[]
     * @return array
     * @throws Throwable
     * @throws StaleObjectException
     */
    private function prepareNotifications($notifications)
    {
        $result = [];
        foreach ($notifications as $notificationRecord) {
            /* @var $notificationRecord Notification */

            try {
                $baseModel = $notificationRecord->getBaseModel();

                if ($baseModel instanceof BaseNotification && $baseModel->validate()) {
                    $result[] = $baseModel;
                } else {
                    throw new IntegrityException('Invalid base model (' . $notificationRecord->class . ') found for notification');
                }
            } catch (IntegrityException $ex) {
                $notificationRecord->delete();
                Yii::warning('Deleted inconsistent notification with id ' . $notificationRecord->id . '. ' . $ex->getMessage());
            }
        }
        return $result;
    }
}
