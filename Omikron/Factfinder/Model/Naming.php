<?php
declare(strict_types=1);
/**
 * Export file naming model.
 *
 * @copyright Copyright © 2018 Brandung GmbH & Co. KG. All rights reserved.
 * @author    CGI <info.de@cgi.com>
 */

namespace Omikron\Factfinder\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Naming
{

    const PREFIX = 'export';

    const FILE_EXTENSION = 'csv';

    const DEFAULT_EXPORT_PATH = 'factfinder';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Get export file name depending on current store view.
     **
     * @return string
     */
    public function getFileName(): string
    {

        return sprintf(
            '%s.%s.%s',
            self::PREFIX,
            $this->getChannel(),
            self::FILE_EXTENSION
        );
    }

    private function getChannel(): string
    {
        return $this->scopeConfig->getValue(
            'factfinder/general/channel',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFilePath()
    {
        return self::DEFAULT_EXPORT_PATH . '/' .$this->getFileName();
    }
}
