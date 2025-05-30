<?php



namespace humhub\modules\stream\models;

use humhub\helpers\ArrayHelper;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\stream\Module;
use Yii;
use yii\base\Exception;

/**
 * StreamSuppressQuery detects same content types in a row and trims the output.
 *
 * E.g. if there are 5 files in a row, only two files will be returned.
 * All following files are stored and can be obtained via method getSuppressed().
 *
 * @see \humhub\modules\stream\actions\Stream
 * @author luke
 * @since 1.2
 */
class StreamSuppressQuery extends StreamQuery
{
    /**
     * @var bool marks query as executed
     */
    protected $isQueryExecuted = false;

    /**
     * @var array suppressed contents (format: [displayedContentId] = [suppressedContentId1, suppressedContentId2])
     */
    protected $suppressions = [];

    /**
     * @var int the last returned content id
     */
    protected $lastContentId;

    /**
     * @var bool return
     */
    protected $suppressionsOnly = false;

    /**
     * @var bool this flag will disable the suppression behaviour if set to true
     */
    protected $preventSuppression = false;

    /**
     * @var int size of suppression row lookup
     */
    public $suppressionScanSize = 300;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['suppressionsOnly', 'boolean', 'strict' => true, 'falseValue' => 'false', 'trueValue' => 'true'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        // Hack to ensure booleans for suppressionsOnly parameter
        if (parent::load($data, $formName)) {
            if ($this->suppressionsOnly == 'true') {
                $this->suppressionsOnly = true;
            } else {
                $this->suppressionsOnly = false;
            }
        }
    }

    /**
     * @since 1.8
     */
    protected function isSuppressionActive()
    {
        /* @var $streamModule Module */
        $streamModule = Yii::$app->getModule('stream');

        return !($this->preventSuppression || $this->limit <= $streamModule->streamSuppressLimit || $this->isSingleContentQuery());
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        // Only suppress on 3 or more contents to deliever
        if (!$this->isSuppressionActive()) {
            $this->isQueryExecuted = true;
            $result = parent::all();

            if (!empty($result)) {
                $last = $result[count($result) - 1];
                $this->lastContentId = $last->id;
            }

            return $result;
        }

        if (!$this->_built) {
            $this->setupQuery();
        }

        if ($this->suppressionsOnly) {
            return $this->allSuppressions();
        }

        $results = [];
        $originalLimit = $this->limit;

        // increase limit
        $this->_query->limit = $this->limit + $this->suppressionScanSize;

        foreach ($this->_query->batch($originalLimit) as $contents) {
            foreach ($contents as $content) {
                $this->lastContentId = $content->id;
                if (!$this->isSuppressed($results, $content)) {
                    $results[] = $content;
                    // Enough results collected
                    if (count($results) === $originalLimit) {
                        break 2;
                    }
                }
            }
        }

        $this->_query->limit = $originalLimit;
        $this->isQueryExecuted = true;

        return $this->postProcessAll($results);
    }

    /**
     * This is a special case, this is used to "load more" of suppressed contents.
     *
     * @return Content[] the list of content objects
     */
    protected function allSuppressions()
    {
        $results = [];
        $originalLimit = $this->limit;

        // increase limit
        $this->_query->limit = $this->limit + $this->suppressionScanSize;

        foreach ($this->_query->batch($originalLimit) as $contents) {
            foreach ($contents as $content) {

                // End of suppression row
                if (isset($results[0]) && $results[0]->object_model != $content->object_model) {
                    break 2;
                }

                $this->lastContentId = $content->id;

                if (count($results) < $originalLimit) {
                    $results[] = $content;
                } else {
                    $this->addSuppression(end($results), $content);
                }
            }
        }

        $this->_query->limit = $originalLimit;
        $this->isQueryExecuted = true;

        return $results;
    }

    /**
     * Checks if this content should be suppressed
     *
     * @param array $results a reference of the current results
     * @param Content $content the content object to check
     *
     * @return bool is suppressed item
     */
    protected function isSuppressed(&$results, $content)
    {
        // Do not surpress if only particual contents are displayed
        if ($this->preventSuppression || !empty($this->includes)) {
            return false;
        }

        /* @var $streamModule Module */
        $streamModule = Yii::$app->getModule('stream');

        // Check if content type is suppressable
        if (in_array($content->object_model, array_merge($streamModule->streamSuppressQueryIgnore, $streamModule->defaultStreamSuppressQueryIgnore))) {
            return false;
        }

        // Checks if previous two contents have the same content class model
        $c = count($results) - 1;
        if ($c >= 1 && $results[$c - 1]->object_model === $results[$c]->object_model && $content->object_model === $results[$c]->object_model) {
            $this->addSuppression($results[$c], $content, ArrayHelper::getColumn($results, 'id'));
            return true;
        }

        return false;
    }

    /**
     * Adds new suppression
     *
     * @param Content $parentContent
     * @param Content $content
     */
    public function addSuppression($parentContent, $content, $parentContentIds = [])
    {
        if (!isset($this->suppressions[$parentContent->id]['parentContent'])) {
            $this->suppressions[$parentContent->id]['parentContent'] = $parentContent;
            $this->suppressions[$parentContent->id]['contentIds'] = [];
        }
        $this->suppressions[$parentContent->id]['contentIds'][] = $content->id;
        $this->suppressions[$parentContent->id]['parentContentIds'][] = $parentContentIds;
    }

    /**
     * Returns suppressed content ids
     *
     * @return array
     * @throws Exception
     */
    public function getSuppressions()
    {
        if (!$this->isQueryExecuted) {
            throw new Exception('Execute query first via all() method before reading suppressed items.');
        }

        $results = [];
        foreach ($this->suppressions as $parentContentId => $infos) {
            /* @var $contentInstance ContentActiveRecord */
            $contentInstance = $infos['parentContent']->getPolymorphicRelation();
            if ($contentInstance === null) {
                Yii::error('Could not load content instance with id: ' . $parentContentId, 'stream');
                continue;
            }

            $results[$parentContentId] = [
                'contentName' => $contentInstance->getContentName(),
                'keys' => $infos['parentContentIds'],
                'message' => Yii::t('StreamModule.base', 'Show {i} more.', ['i' => count($infos['contentIds'])]),
            ];
        }

        return $results;
    }

    /**
     * Returns the last content id of the stream query.
     * It may also contains a suppressed content id.
     *
     * @return int content id
     */
    public function getLastContentId()
    {
        return $this->lastContentId;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'StreamQuery';
    }

}
