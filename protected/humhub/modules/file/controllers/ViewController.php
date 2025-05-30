<?php



namespace humhub\modules\file\controllers;

use humhub\components\Controller;
use Yii;
use yii\web\HttpException;
use humhub\components\behaviors\AccessControl;
use humhub\modules\file\models\File;
use humhub\modules\file\handler\FileHandlerCollection;

/**
 * ViewControllers provides the open modal for files
 *
 * @since 1.2
 */
class ViewController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::class,
                'guestAllowedActions' => ['index'],
            ],
        ];
    }

    public function actionIndex()
    {
        $guid = Yii::$app->request->get('guid');
        $file = File::findOne(['guid' => $guid]);

        if (!$file) {
            throw new HttpException(404, Yii::t('FileModule.base', 'Could not find requested file!'));
        }

        $viewHandler = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_VIEW, $file);
        $exportHandler = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_EXPORT, $file);

        $editHandler = [];
        $importHandler = [];
        if ($file->canDelete()) {
            $editHandler = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_EDIT, $file);
            $importHandler = FileHandlerCollection::getByType(FileHandlerCollection::TYPE_IMPORT, $file);
        }

        return $this->renderAjax('index', [
            'file' => $file,
            'importHandler' => $importHandler,
            'exportHandler' => $exportHandler,
            'editHandler' => $editHandler,
            'viewHandler' => $viewHandler,
        ]);
    }

}
