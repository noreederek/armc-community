<?php



namespace humhub\modules\admin\grid;

use yii\grid\ActionColumn;

/**
 * ApprovalActionColumn
 */
class ApprovalActionColumn extends ActionColumn
{
    public $template = '{view} {sendMessage} {update} {delete}';
}
