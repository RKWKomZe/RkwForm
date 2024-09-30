<?php
namespace RKW\RkwForm\Domain\Finishers;

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

use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\ViewHelpers\RenderRenderableViewHelper;

/**
 * Class SendToUrl
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SendToUrlFinisher extends AbstractFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = [
        'url' => 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8',
        'method' => 'POST',
        'additionalParams' => [],
        'removeArrayKeyNumbers' => false,
        'templateName' => 'ConfirmationSentToUrl',
        'showConfirmationMessage' => false,
        'jsonResultCodeKey' => 'result',
        'jsonResultMessageKey' => 'msg',
        'values' => [],
        'mapping' => [],
        'filter' => [],
    ];


    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     */
    protected function executeInternal(): string
    {
        try {

            $url = $this->parseOption('url');
            $method = in_array(strtoupper($this->parseOption('method')), ['POST', 'GET']) ? $this->parseOption('method') : 'POST';
            $showConfirmationMessage = $this->parseOption('showConfirmationMessage');
            $jsonResultCodeKey = $this->parseOption('jsonResultCodeKey');
            $jsonResultMessageKey = $this->parseOption('jsonResultMessageKey');
            $removeArrayKeyNumbers = $this->parseOption('removeArrayKeyNumbers');
            $dataArray = $this->parseOption('additionalParams');
            $mapping = $this->parseOption('mapping');
            $filter = $this->parseOption('filter');
            $values = $this->parseOption('values');

            foreach ($values as $key => $value) {

                // map field-values
                if (isset($mapping[$key])) {
                    if (is_array($value)) {
                        $tempArray = [];
                        foreach ($value as $subValue) {
                            if (isset($mapping[$key][$subValue])) {
                                $tempArray[] = $mapping[$key][$subValue];
                            } else {
                                $tempArray[] = $subValue;
                            }
                        }
                        $dataArray[$key] = array_unique($tempArray);
                    } else {
                        if (isset($mapping[$key][$value])) {
                            $dataArray[$key] = $mapping[$key][$value];
                        } else {
                            $dataArray[$key] = $value;
                        }
                    }
                } else {
                    $dataArray[$key] = $value;
                }

                // filter field-values
                if (isset($filter[$key])) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            if (in_array($subValue, $filter[$key])) {
                                unset($dataArray[$key][$subKey]);
                            }
                        }
                    } else {
                        if (in_array($value, $filter[$key])) {
                            unset($dataArray[$key]);
                        }
                    }
                }
            }

            // finally remove empty values
            foreach ($dataArray as $key => $value) {
                if (
                    (is_string($value) && str_starts_with($value, '{') && str_ends_with($value, '}'))
                    || (empty($value) && $value !== 0)
                ) {
                    unset ($dataArray[$key]);
                }
            }

            // build query
            $queryString = http_build_query($dataArray);

            // remove key-numbers from array-values - needed for e.g. SalesForce!
            if ($removeArrayKeyNumbers) {
                $queryString = preg_replace( '/%5B\d+%5D/','', $queryString);
            }

            if ($method == 'POST') {
                $options = [
                    'http' => [ // use key 'http' even if you send the request to https://...
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method' => $method,
                        'content' => $queryString
                    ],
                ];

                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);

            } else {
                $result = file_get_contents($url . '?' . $queryString);
            }

            if ($result === false) {
                throw new FinisherException('The data could not be submitted.', 1701203860);
            }

            // show confirmation message if enabled!
            if ($showConfirmationMessage) {

                $message = $result;
                $code = 'error';
                if ($jsonResult = json_decode($result, true)) {
                   if ($jsonResult[$jsonResultMessageKey]) {
                       $message = $jsonResult[$jsonResultMessageKey];
                   }
                   if ($jsonResult[$jsonResultCodeKey]) {
                       $code = $jsonResult[$jsonResultCodeKey];
                   }
                }

                $this->finisherContext->getFinisherVariableProvider()->add(
                    $this->shortFinisherIdentifier,
                    'message',
                    $message
                );
                $this->finisherContext->getFinisherVariableProvider()->add(
                    $this->shortFinisherIdentifier,
                    'code',
                    $code
                );

                $standaloneView = $this->initializeStandaloneView(
                    $this->finisherContext->getFormRuntime()
                );

                $standaloneView->assignMultiple([
                    'code' => $code,
                    'message' => $message,
                ]);

                return $standaloneView->render();
            }

            return '';

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1701203860);
        }
    }


    /**
     * @param FormRuntime $formRuntime
     * @return StandaloneView
     * @throws FinisherException
     */
    protected function initializeStandaloneView(FormRuntime $formRuntime): StandaloneView
    {
        $standaloneView = $this->objectManager->get(StandaloneView::class);

        if (!isset($this->options['templateName'])) {
            throw new FinisherException(
                'The option "templateName" must be set for the ConfirmationFinisher.',
                1521573955
            );
        }

        $standaloneView->setTemplate($this->options['templateName']);
        $standaloneView->getTemplatePaths()->fillFromConfigurationArray($this->options);

        if (isset($this->options['variables']) && is_array($this->options['variables'])) {
            $standaloneView->assignMultiple($this->options['variables']);
        }

        $standaloneView->assign('form', $formRuntime);
        $standaloneView->assign('finisherVariableProvider', $this->finisherContext->getFinisherVariableProvider());

        $standaloneView->getRenderingContext()
            ->getViewHelperVariableContainer()
            ->addOrUpdate(RenderRenderableViewHelper::class, 'formRuntime', $formRuntime);

        return $standaloneView;
    }
}
