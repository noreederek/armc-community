<?php



namespace humhub\components\access;

class DelegateAccessValidator extends ActionAccessValidator
{
    public $owner;

    public $handler;

    /**
     * @var string Name of callback method to run after failed validation
     * @since 1.8
     */
    public $codeCallback;

    /**
     * @inheritDoc
     */
    protected function validate($rule)
    {
        $handler = $this->handler;
        return $this->owner->$handler($rule, $this);
    }
}
