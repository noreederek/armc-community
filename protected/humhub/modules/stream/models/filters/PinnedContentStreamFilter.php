<?php



namespace humhub\modules\stream\models\filters;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;

/**
 * This stream filter manages the stream order of container streams with pinned content support.
 * This filter can not be deactivated by request parameter.
 *
 * This filter will fetch pinned content entries of the given [[container]] respecting all active stream filters
 * and exclude the pinned content entries from the stream query result.
 *
 * The pinned content entries can be accessed by [[pinnedContent]] property after this filter was applied.
 *
 * @package humhub\modules\stream\models\filters
 * @since 1.6
 */
class PinnedContentStreamFilter extends StreamQueryFilter
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    /**
     * @var Content[]
     */
    private $pinnedContent = [];

    /**
     * @inheritDoc
     */
    public function apply()
    {
        // Currently we only support pinned entries on container streams
        if (!$this->container) {
            return;
        }

        if ($this->streamQuery->isInitialQuery()) {
            $pinnedContentIds = $this->fetchPinnedContent();

            // Exclude pinned content from result, we've already fetched and cached them
            if (!empty($pinnedContentIds)) {
                $this->query->andWhere((['NOT IN', 'content.id', $pinnedContentIds]));
            }
        } elseif (!$this->streamQuery->isSingleContentQuery()) {
            // All pinned entries of this container were loaded within the initial request, so don't include them here!
            $this->query->andWhere(['OR', ['content.pinned' => 0], ['<>', 'content.contentcontainer_id', $this->container->contentcontainer_id]]);
        }
    }

    /**
     * @inheritDoc
     */
    public function postProcessStreamResult(array &$results): void
    {
        $results = array_merge($this->pinnedContent, $results);
    }

    /**
     * Loads pinned content entries into [[pinnedContent]] by means of a cloned stream query.
     * @return array array of pinned content ids
     */
    private function fetchPinnedContent(): array
    {
        $pinnedQuery = clone $this->query;
        if (!empty($this->streamQuery->stateFilterCondition)) {
            $pinnedQuery->andWhere($this->streamQuery->stateFilterCondition);
        }
        $pinnedQuery->andWhere(['content.pinned' => 1]);
        $pinnedQuery->andWhere(['content.contentcontainer_id' => $this->container->contentcontainer_id]);
        $pinnedQuery->limit(1000);
        $this->pinnedContent = $pinnedQuery->all();
        return array_map(function ($content) {
            return $content->id;
        }, $this->pinnedContent);
    }
}
