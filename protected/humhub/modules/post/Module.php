<?php



namespace humhub\modules\post;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\post\models\Post;

/**
 * Post Submodule
 *
 * @author Luke
 * @since 0.5
 */
class Module extends ContentContainerModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\post\controllers';

    /**
     * @since 1.14
     * @var bool Automatically increase font size for short posts.
     */
    public bool $enableDynamicFontSize = false;

    /**
     * @since 1.15
     * @var int collapsed post block height
     */
    public int $collapsedPostHeight = 300;

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer !== null) {
            return [
                new permissions\CreatePost(),
            ];
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function getContentClasses(?ContentContainerActiveRecord $contentContainer = null): array
    {
        return [Post::class];
    }
}
