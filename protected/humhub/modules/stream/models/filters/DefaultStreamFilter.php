<?php



namespace humhub\modules\stream\models\filters;

use humhub\modules\content\models\Content;
use humhub\modules\space\models\Space;
use Yii;
use yii\db\Query;

class DefaultStreamFilter extends StreamQueryFilter
{
    /**
     * Default filters
     */
    public const FILTER_FILES = "entry_files";
    public const FILTER_ARCHIVED = "entry_archived";
    public const FILTER_MINE = "entry_mine";
    public const FILTER_INVOLVED = "entry_userinvolved";
    public const FILTER_PRIVATE = "visibility_private";
    public const FILTER_PUBLIC = "visibility_public";
    public const FILTER_HIDDEN = "entry_hidden";

    /**
     * Array of stream filters to apply to the query.
     * There are the following filter available:
     *
     *  - 'entry_files': Filters content with attached files
     *  - 'entry_mine': Filters only content created by the query $user
     *  - 'entry_userinvovled': Filter content the query $user is involved
     *  - 'visibility_private': Filter only private content
     *  - 'visibility_public': Filter only public content
     *
     * @var array
     */
    public $filters = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filters'], 'safe'],
        ];
    }

    public function init()
    {
        $this->filters = $this->streamQuery->filters;
        parent::init();
        $this->filters = (is_string($this->filters)) ? [$this->filters] : $this->filters;
    }

    public function apply()
    {
        if ($this->isFilterActive(self::FILTER_FILES)) {
            $this->filterFile();
        }

        if ($this->isFilterActive(self::FILTER_ARCHIVED)) {
            $this->filterArchived();
        } elseif (!$this->streamQuery->isSingleContentQuery()) {
            // Only omit archived content by default when we load more than one entry
            $this->unFilterArchived();
        }

        // Show only mine items
        if ($this->isFilterActive(self::FILTER_MINE)) {
            $this->filterMine();
        }

        // Show only items where the current user is invovled
        if ($this->isFilterActive(self::FILTER_INVOLVED)) {
            $this->filterInvolved();
        }

        // Visibility filters
        if ($this->isFilterActive(self::FILTER_PRIVATE)) {
            $this->filterPrivate();
        } elseif ($this->isFilterActive(self::FILTER_PUBLIC)) {
            $this->filterPublic();
        }

        if ($this->isFilterActive(self::FILTER_HIDDEN)) {
            $this->filterHidden();
        } elseif (!$this->streamQuery->isSingleContentQuery()) {
            // Only omit hidden content by default when we load more than one entry
            $this->unFilterHidden();
        }

    }

    public function isFilterActive($filter)
    {
        return in_array($filter, $this->filters);
    }

    protected function filterFile()
    {
        $fileSelector = (new Query())
            ->select(["id"])
            ->from('file')
            ->where('file.object_model=content.object_model AND file.object_id=content.object_id')
            ->limit(1);

        $fileSelectorSql = Yii::$app->db->getQueryBuilder()->build($fileSelector)[0];
        $this->query->andWhere('(' . $fileSelectorSql . ') IS NOT NULL');
        return $this;
    }

    protected function unFilterArchived()
    {
        $this->query->leftJoin('space AS spaceArchived', 'contentcontainer.pk = spaceArchived.id AND contentcontainer.class = :spaceClass', [':spaceClass' => Space::class]);

        if (!empty($this->streamQuery->container->contentcontainer_id)) {
            $this->query->andWhere(
                '(spaceArchived.status != :statusArchived OR spaceArchived.status IS NULL OR spaceArchived.contentcontainer_id = :containerId)',
                [':statusArchived' => Space::STATUS_ARCHIVED, ':containerId' => $this->streamQuery->container->contentcontainer_id],
            );
        } else {
            $this->query->andWhere('(spaceArchived.status != :statusArchived OR spaceArchived.status IS NULL)', [':statusArchived' => Space::STATUS_ARCHIVED]);
        }

        $this->query->andWhere('(content.archived != 1 OR content.archived IS NULL OR spaceArchived.status = :statusArchived)', [':statusArchived' => Space::STATUS_ARCHIVED]);
        return $this;
    }

    protected function filterArchived()
    {
        $this->query->leftJoin('space AS spaceArchived', 'contentcontainer.pk = spaceArchived.id AND contentcontainer.class = :spaceClass', [':spaceClass' => Space::class]);
        $this->query->andWhere('(content.archived = 1 OR spaceArchived.status = :statusArchived)', [':statusArchived' => Space::STATUS_ARCHIVED]);
        return $this;
    }

    protected function filterMine()
    {
        if ($this->streamQuery->user) {
            $this->query->andWhere(['content.created_by' => $this->streamQuery->user->id]);
        }
        return $this;
    }

    protected function filterInvolved()
    {
        if ($this->streamQuery->user) {
            $this->query->leftJoin('user_follow AS user_involved', 'content.object_model=user_involved.object_model AND content.object_id=user_involved.object_id AND user_involved.user_id = :userId', ['userId' => $this->streamQuery->user->id]);
            $this->query->andWhere("user_involved.id IS NOT NULL");
        }
        return $this;
    }

    protected function filterPublic()
    {
        $this->query->andWhere(['content.visibility' => Content::VISIBILITY_PUBLIC]);
        return $this;
    }

    protected function filterPrivate()
    {
        $this->query->andWhere(['content.visibility' => Content::VISIBILITY_PRIVATE]);
        return $this;
    }

    private function filterHidden()
    {
        $this->query->andWhere(['content.hidden' => 1]);
        return $this;
    }

    private function unFilterHidden()
    {
        $this->query->andWhere(['content.hidden' => 0]);
        return $this;
    }
}
