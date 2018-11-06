<?php
declare(strict_types=1);
/**
 * Links to export downloads.
 *
 * @copyright Copyright © 2018 Brandung GmbH & Co. KG. All rights reserved.
 * @author    CGI <info.de@cgi.com>
 */

namespace Omikron\Factfinder\Block\Adminhtml\System;

use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Omikron\Factfinder\Model\Naming;


class Links extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Url
     */
    private $frontendUrl;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Naming
     */
    private $naming;

    public function __construct(
        Context $context,
        Url $frontendUrl,
        StoreManagerInterface $storeManager,
        Naming $naming
    ) {
        parent::__construct($context);
        $this->frontendUrl = $frontendUrl;
        $this->storeManager = $storeManager;
        $this->setTemplate('Omikron_Factfinder::system/config/links.phtml');
        $this->naming = $naming;
    }

    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element)
    {
        $html = '<td class="label"><label for="' . $element->getHtmlId() . '">' .
            $element->getLabel() .
            '</label></td>';
        $html .= $this->toHtml();
        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Returns array of data for rendering links in the template
     *
     * @return array
     */
    public function getDownloadLinks()
    {
        $downloadLinks = [];
        $downloadLinks[] = [
            'url' => $this->frontendUrl->getUrl('factfinder/download', ['_nosid' => true]),
            'label' => sprintf('file: %s (last modified at: %s)', $this->naming->getFileName(), date('Y-m-d H:i:s', $this->getExportMTime()) )
        ];

        return $downloadLinks;
    }

    public function getExportMTime()
    {
        $filePath = $this->naming->getFilePath();

        if (file_exists($filePath)) {
            return filemtime($filePath);
        } else {
            return 0;
        }
    }
}
