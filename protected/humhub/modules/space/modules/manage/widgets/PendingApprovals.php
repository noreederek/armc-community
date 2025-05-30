<?php



namespace humhub\modules\space\modules\manage\widgets;

use humhub\modules\space\models\Space;
use yii\base\Widget;

/**
 * PendingApprovals show open member approvals to admin in sidebar
 *
 * @author Luke
 * @since 0.21
 */
class PendingApprovals extends Widget
{
    /**
     * @var Space
     */
    public $space;

    /**
     * @var int number of applicants to show
     */
    public $maxApplicants = 15;

    /**
     * @inheritdoc
     */
    public function run()
    {
        // Only visible for admins
        if (!$this->space->isAdmin()) {
            return;
        }

        $applicants = $this->space->getApplicants()->limit($this->maxApplicants)->all();

        // No applicants
        if (count($applicants) === 0) {
            return;
        }

        return $this->render('pendingApprovals', ['applicants' => $applicants, 'space' => $this->space]);
    }

}
