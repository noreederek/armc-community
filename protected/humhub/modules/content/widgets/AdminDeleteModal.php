<?php



namespace humhub\modules\content\widgets;

use humhub\modules\content\models\forms\AdminDeleteContentForm;
use yii\base\Widget;

/**
 * Admin Delete Modal for Wall Entries
 *
 * This widget will be shown when admin deletes someone's content
 *
 */
class AdminDeleteModal extends Widget
{
    /**
     * @var AdminDeleteContentForm
     */
    public $model = null;

    /**
     * Executes the widget.
     */
    public function run()
    {
        return $this->render('adminDeleteModal', [
            'model' => $this->model,
        ]);
    }
}
