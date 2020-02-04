<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\VeniaCatalogSampleData\Setup\Patch\Data;


use Magento\Framework\Setup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use MagentoEse\VeniaCatalogSampleData\Setup\Installer;

class OldInstallData implements DataPatchInterface,PatchVersionInterface
{
    /**
     * @var Setup\SampleData\Executor
     */
    protected $executor;

    /**
     * @var Installer
     */
    protected $installer;

    /**
     * OldInstallData constructor.
     * @param Setup\SampleData\Executor $executor
     * @param Installer $installer
     */
    public function __construct(
        Setup\SampleData\Executor $executor,
        Installer $installer
    ) {
        $this->executor = $executor;
        $this->installer = $installer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->executor->exec($this->installer);
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public static function getVersion()
    {
       return '0.0.2';
    }
}
