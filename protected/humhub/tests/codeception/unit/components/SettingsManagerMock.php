<?php



namespace humhub\tests\codeception\unit\components;

use humhub\components\SettingsManager;

class SettingsManagerMock extends SettingsManager
{
    public bool $usedFind = false;

    protected function find()
    {
        $this->usedFind = true;

        return parent::find();
    }

    public function getCacheKey(): string
    {
        return parent::getCacheKey();
    }

    /**
     * @return bool
     */
    public function didAccessDB(): bool
    {
        $read = $this->usedFind;
        $this->usedFind = false;
        return $read;
    }

    public function invalidateCache()
    {
        parent::invalidateCache();
    }
}
