<?php



namespace humhub\modules\user\jobs;

use humhub\modules\queue\ActiveJob;
use humhub\modules\user\models\Session;

/**
 * DeleteExpiredSessions cleanups the session table.
 *
 * @since 1.3
 * @author Luke
 */
class DeleteExpiredSessions extends ActiveJob
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        foreach (Session::find()->where(['<', 'expire', time()])->all() as $session) {
            $session->delete();
        }
    }

}
