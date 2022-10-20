<?php
namespace RKW\RkwForm\ViewHelpers;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class GetFinisherOptionViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GetFinisherOptionViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('formIdentifier', 'string', 'Really, I don\'t know what\'s that for', true);
        $this->registerArgument('finisherIdentifier', 'string', 'Really, I don\'t know what\'s that for', true);
        $this->registerArgument('getOption', 'string', 'Really, I don\'t know what\'s that for', true);
    }


    /**
     * resolves problem of protected finisher options. Get them via YamlFileLoader!
     *
     * @param array $arguments
     * @param \Closure  $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface
        $renderingContext
    ): string {

        $formIdentifier = $arguments['formIdentifier'];
        $finisherIdentifier = $arguments['finisherIdentifier'];
        $getOption = $arguments['getOption'];

        /** @var \TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader $yamlFileLoader */
        $yamlFileLoader = GeneralUtility::makeInstance(YamlFileLoader::class);
        $formConfiguration = $yamlFileLoader->load('EXT:rkw_form/Configuration/Yaml/Forms/' . $formIdentifier . '.form.yaml');

        foreach ($formConfiguration['finishers'] as $finisher) {
            if ($finisher['identifier'] == $finisherIdentifier) {
                if (array_key_exists($getOption, $finisher['options'])) {
                    // ATTENTION: If the value is a variable, the "{}" signs will be removed!
                    return trim($finisher['options'][$getOption], '{}');
                }
            }
        }

        return '';
    }


}
