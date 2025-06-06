<?php



namespace humhub\modules\stream\models\filters;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This stream filter will only include content related to a given [[ContentContainerActiveRecord]] and furthermore
 * only includes private content if the query user is allowed to access private content of this container.
 *
 * @package humhub\modules\stream\models\filters
 * @since 1.6
 */
class ContentContainerStreamFilter extends StreamQueryFilter
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function apply()
    {
        if (!$this->container) {
            return;
        }

        $user = $this->streamQuery->user;

        // Limit to this content container
        $this->query->andWhere(['content.contentcontainer_id' => $this->container->contentcontainer_id]);

        // Limit to public posts when no member
        if (!$this->container->canAccessPrivateContent($user)) {
            if (Yii::$app->user->isGuest) {
                $this->query->andWhere('content.visibility = :visibility', [':visibility' => Content::VISIBILITY_PUBLIC]);
            } elseif (!Yii::$app->user->getIdentity()?->canManageAllContent()) {
                // Limit only if current User/Admin cannot view all content
                $this->query->andWhere('content.visibility = :visibility OR content.created_by = :userId', [
                    ':visibility' => Content::VISIBILITY_PUBLIC,
                    ':userId' => Yii::$app->user->id,
                ]);
            }
        }
    }
}
