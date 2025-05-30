<?php



namespace Some\Name\Space\moduleWithMigration;

class Module extends \humhub\components\Module
{
    public const ID = 'moduleWithMigration';
    public const NAMESPACE = __NAMESPACE__;
    public bool $doEnable = true;
    public bool $doDisable = true;

    public function beforeEnable(): bool
    {
        return $this->doEnable;
    }

    public function beforeDisable(): bool
    {
        return $this->doDisable;
    }
}
