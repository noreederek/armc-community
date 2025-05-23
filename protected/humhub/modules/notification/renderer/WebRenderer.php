<?php



namespace humhub\modules\notification\renderer;

use humhub\components\rendering\DefaultViewPathRenderer;

/**
 * The WebTargetRenderer is used to render Notifications for the WebTarget.
 *
 * @see \humhub\modules\notification\targets\WebTarget
 * @author buddha
 */
class WebRenderer extends DefaultViewPathRenderer
{
    /**
     * @inheritdoc
     */
    public $defaultView = '@notification/views/default.php';

    /**
     * @inheritdoc
     */
    public $defaultViewPath = '@notification/views';
}
