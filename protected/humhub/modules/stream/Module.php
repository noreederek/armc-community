<?php



namespace humhub\modules\stream;

use humhub\modules\activity\models\Activity;
use humhub\modules\post\models\Post;

/**
 * Stream Module provides stream (wall) backend and frontend
 *
 * @author Luke
 * @since 1.2
 */
class Module extends \humhub\components\Module
{
    /**
     * @var array content classes to excludes from streams
     */
    public $streamExcludes = [];

    /**
     * @var array content classes which are not suppressed when in a row
     */
    public $streamSuppressQueryIgnore = [];

    /**
     * @var array default content classes which are not suppressed when in a row
     */
    public $defaultStreamSuppressQueryIgnore = [
        Post::class,
        Activity::class,
    ];

    /**
     * @var int number of contents from which "Show more" appears in the stream
     */
    public $streamSuppressLimit = 2;

    /**
     * @var bool show contents of deactivated users in stream
     */
    public $showDeactivatedUserContent = true;

}
