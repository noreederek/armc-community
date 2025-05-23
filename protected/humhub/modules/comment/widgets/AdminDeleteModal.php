<?php



namespace humhub\modules\comment\widgets;

use humhub\modules\comment\models\forms\AdminDeleteCommentForm;
use yii\base\Widget;

/**
 * Admin Delete Modal for Comments
 *
 * This widget will be shown when admin deletes someone's comment
 *
 */
class AdminDeleteModal extends Widget
{
    /**
     * @var AdminDeleteCommentForm
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
