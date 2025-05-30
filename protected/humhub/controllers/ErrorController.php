<?php



namespace humhub\controllers;

use humhub\modules\user\helpers\AuthHelper;
use Yii;
use yii\web\HttpException;
use yii\base\UserException;
use humhub\components\Controller;

/**
 * ErrorController
 *
 * @author luke
 * @since 0.11
 */
class ErrorController extends Controller
{
    /**
     * This is the action to handle external exceptions.
     */
    public function actionIndex()
    {
        // Fix: https://github.com/humhub/humhub/issues/3848
        Yii::$app->view->theme->register();

        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            return '';
        }

        if ($exception instanceof UserException || $exception instanceof HttpException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('error', 'An internal server error occurred.');
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';
            return [
                'error' => true,
                'message' => $message,
            ];
        }

        /**
         * Show special login required view for guests
         */
        if (Yii::$app->user->isGuest && $exception instanceof HttpException && $exception->statusCode == '401' && AuthHelper::isGuestAccessEnabled()) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->getAbsoluteUrl());
            return $this->render('@humhub/views/error/401_guests', ['message' => $message]);
        }

        return $this->render('@humhub/views/error/index', [
            'message' => $message,
        ]);
    }
}
