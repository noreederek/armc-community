<?php



namespace humhub\modules\user\controllers;

use Exception;
use humhub\components\behaviors\AccessControl;
use humhub\modules\user\models\User;
use humhub\modules\user\permissions\CanMention;
use humhub\modules\user\widgets\Image;
use humhub\modules\user\widgets\UserPicker;
use Yii;
use yii\web\Controller;

/**
 * Search Controller provides action for searching users.
 *
 * @author Luke
 * @since 0.5
 */
class SearchController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::class,
            ],
        ];
    }

    /**
     * JSON Search for Users
     *
     * Returns an array of users with fields:
     *  - guid
     *  - displayName
     *  - image
     *  - profile link
     */
    public function actionJson()
    {
        Yii::$app->response->format = 'json';

        return UserPicker::filter([
            'keyword' => Yii::$app->request->get('keyword'),
            'fillUser' => true,
            'disableFillUser' => false,
        ]);
    }


    /**
     * JSON Search interface for Mentioning
     *
     * @throws Exception
     */
    public function actionMentioning()
    {
        Yii::$app->response->format = 'json';

        $results = [];

        $query = User::find()->visible()->search((string)Yii::$app->request->get('keyword'));

        foreach ($query->limit(10)->all() as $container) {
            if ($container->permissionManager->can(CanMention::class)) {
                $results[] = [
                    'guid' => $container->guid,
                    'type' => 'u',
                    'name' => $container->getDisplayName(),
                    'image' => Image::widget(['user' => $container, 'width' => 20]),
                    'link' => $container->getUrl(),
                ];
            }
        }

        return $this->asJson($results);
    }
}
