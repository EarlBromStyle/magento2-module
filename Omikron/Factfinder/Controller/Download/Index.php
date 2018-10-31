<?php
declare(strict_types=1);
/**
 * Download exported file.
 *
 * @copyright Copyright © 2018 Brandung GmbH & Co. KG. All rights reserved.
 * @author    CGI <info.de@cgi.com>
 */

namespace Omikron\Factfinder\Controller\Download;

use Omikron\Factfinder\Model\Naming;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var Naming
     */
    private $naming;

    /**
     * @const string Default export path for cron job.
     */
    const DEFAULT_EXPORT_PATH = 'factfinder';

    public function __construct(
        Context $context,
        Filesystem $filesystem,
        FileFactory $fileFactory,
        Naming $naming
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->naming = $naming;
    }

    /**
     * @return ResponseInterface|Raw
     * @throws NotFoundException
     */
    public function execute()
    {

        $fileName = $this->naming->getFileName();
        $relativeFilePath = self::DEFAULT_EXPORT_PATH . '/' . $fileName;

        $directory = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $fileIsReadable = $directory->isFile($relativeFilePath) && $directory->isReadable($relativeFilePath);

        if (!$fileIsReadable) {
            return $this->getNoFileResult();
        }

        try {
            $fileContents = $directory->readFile($relativeFilePath);
            /** @var \Magento\Framework\App\Response\Http $result */
            $result = $this->fileFactory->create(
                $fileName,
                null,
                DirectoryList::ROOT,
                'application/octet-stream',
                strlen($fileContents)
            );
            $result->setBody($fileContents);
        } catch (\Exception $e) {
            $result = $this->getNoFileResult();
        }

        return $result;
    }

    /**
     * Get result for the case when file is not accessible.
     *
     * @return Raw
     */
    private function getNoFileResult(): Raw
    {
        /** @var Raw $rawResult */
        $rawResult = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $rawResult->setContents(
            __('No export file was generated yet. Please trigger an export first. (export path was: ' . self::DEFAULT_EXPORT_PATH . '/' . $this->naming->getFileName() . ' )')
        );

        return $rawResult;
    }
}
