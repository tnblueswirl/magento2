<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shell model, used to work with logs via command line
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Log\Model;

class Shell extends \Magento\Core\Model\AbstractShell
{
    /**
     * @var \Magento\Log\Model\Shell\Command\Factory
     */
    protected $_commandFactory;

    /**
     * @param \Magento\Log\Model\Shell\Command\Factory $commandFactory
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\App\Dir $entryPoint
     * @param \Magento\App\Dir $dir
     */
    public function __construct(
        \Magento\Log\Model\Shell\Command\Factory $commandFactory,
        \Magento\Filesystem $filesystem,
        $entryPoint,
        \Magento\App\Dir $dir
    ) {
        parent::__construct($filesystem, $entryPoint, $dir);
        $this->_commandFactory = $commandFactory;
    }

    /**
     * Runs script
     *
     * @return \Magento\Log\Model\Shell
     */
    public function run()
    {
        if ($this->_showHelp()) {
            return $this;
        }

        if ($this->getArg('clean')) {
            $output = $this->_commandFactory->createCleanCommand($this->getArg('days'))->execute();
        } elseif ($this->getArg('status')) {
            $output = $this->_commandFactory->createStatusCommand()->execute();
        } else {
            $output = $this->getUsageHelp();
        }

        echo $output;

        return $this;
    }

    /**
     * Retrieves usage help message
     *
     * @return string
     */
    public function getUsageHelp()
    {
        return <<<USAGE
Usage:  php -f {$this->_entryPoint} -- [options]
        php -f {$this->_entryPoint} -- clean --days 1

  clean             Clean Logs
  --days <days>     Save log, days. (Minimum 1 day, if defined - ignoring system value)
  status            Display statistics per log tables
  help              This help

USAGE;
    }
}
