<?php

namespace Rebel\Component\Setting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Rebel\Component\Setting\Contracts\Setting as SettingInterface;

class ChangeSetting implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $setting;

    private $changeBy;

    public function __construct(SettingInterface $setting, $changeBy)
    {
        $this->setting = $setting;
        $this->changeBy = $changeBy;
    }

    public function handle()
    {
        $this->setting
            ->setUser($this->changeBy)
            ->udpateChecksum();

        $this->removeConfigurationCache();
    }

    private function removeConfigurationCache()
    {
        $name = app('config')->get('nebo.configuration_cache_name');

        app('cache.store')->forget($name);
    }
}
