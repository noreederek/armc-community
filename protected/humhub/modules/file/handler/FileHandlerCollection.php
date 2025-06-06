<?php



namespace humhub\modules\file\handler;

use humhub\modules\file\models\File;
use humhub\modules\file\Module;
use Yii;
use yii\base\Component;

/**
 * FileHandlerCollection
 *
 * @since 1.2
 * @author Luke
 */
class FileHandlerCollection extends Component
{
    /**
     * @event the init event - use to register file handlers
     */
    public const EVENT_INIT = 'init';

    /**
     * Collection Types
     */
    public const TYPE_VIEW = 'view';
    public const TYPE_IMPORT = 'import';
    public const TYPE_EXPORT = 'export';
    public const TYPE_CREATE = 'create';
    public const TYPE_EDIT = 'edit';

    /**
     * @var string current collection type
     */
    public $type;

    /**
     * @var File
     */
    public $file = null;

    /**
     * @var BaseFileHandler[]
     */
    public $handlers = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        try {
            $this->trigger(self::EVENT_INIT);
        } catch (\Exception $ex) {
            Yii::error('Could not init file handler. Error: ' . $ex->getMessage(), 'file');
        }

        // Register default handlers
        if ($this->type === self::TYPE_CREATE) {
            /** @var Module $module */
            $module = Yii::$app->getModule('file');
            foreach ($module->defaultFileHandlers as $handlerClass) {
                $this->register(new $handlerClass());
            }
        }

        // Register Core Handler
        if ($this->type === self::TYPE_EXPORT) {
            $this->register(Yii::createObject(['class' => DownloadFileHandler::class]));
        }

        $this->sortHandler();
    }

    /**
     * @param \humhub\modules\file\components\BaseFileHandler $handler
     */
    public function register(BaseFileHandler $handler)
    {
        $handler->file = $this->file;
        $this->handlers[] = $handler;
    }

    /**
     * Returns registered handlers by type
     *
     * @param string|array $type or multiple type array
     * @param File $file the file (optional)
     * @return BaseFileHandler[] the registered handlers
     */
    public static function getByType($types, $file = null)
    {
        $handlers = [];

        if (!is_array($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
            $handlers = array_merge($handlers, Yii::createObject([
                'class' => static::class,
                'file' => $file,
                'type' => $type,
            ])->handlers);
        }
        return $handlers;
    }

    /**
     * Sorts the registered handlers
     */
    protected function sortHandler()
    {
        usort($this->handlers, function (BaseFileHandler $a, BaseFileHandler $b) {
            return strcmp($a->position, $b->position);
        });
    }

}
