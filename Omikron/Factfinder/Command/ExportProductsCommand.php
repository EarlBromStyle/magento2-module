<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */
namespace Omikron\Factfinder\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Omikron\Factfinder\Model\Export\Product\Proxy as ProductExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * ExportProductsCommand
 */
class ExportProductsCommand extends Command
{
    /**
     * @var ProductExporter
     */
    private $productExporter;
    /**
     * @var AppState
     */
    private $appState;
    /**
     * ExportProductsCommand constructor.
     *
     * @param ProductExporter $productExporter
     * @param AppState $appState
     */
    public function __construct(ProductExporter $productExporter, AppState $appState)
    {
        parent::__construct();
        $this->productExporter = $productExporter;
        $this->appState = $appState;
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('factfinder:product-export');
        $this->setDescription('Generates DI configuration and all missing classes that can be auto-generated');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // Rather set it on execution (and check if its already set, of course)
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
            // intentionally left empty
        }
        $this->productExporter->exportProducts();
    }
}