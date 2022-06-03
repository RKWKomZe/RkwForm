<?php
namespace RKW\RkwForm\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use RKW\RkwForm\Domain\Model\DoubleOptinForm;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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

/**
 * Class DoubleOptinFormController
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class DoubleOptinFormController extends \RKW\RkwForm\Controller\AbstractFormController
{

    /**
     * doubleOptinFormRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\DoubleOptinFormRepository
     * @inject
     */
    protected $doubleOptinFormRepository = null;

    /**
     * objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * persistenceManager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * action create
     *
     * @param \RKW\RkwForm\Domain\Model\DoubleOptinForm $standardForm
     * @param int $privacy
     * @validate $standardForm \RKW\RkwForm\Validation\Validator\DoubleOptinFormValidator
     * @return void
     */
    public function createAction(DoubleOptinForm $standardForm, $privacy = 0)
    {

        //  set type
        $standardForm->setType('doubleOptin');

        //  set token
        $standardForm->setToken(sha1(rand()));

        //  set verificationLink -> muss aus der FlexForm kommen bzw. muss die gleiche Seite sein?
        $verifypid = 9116;  //  @todo: gleiche verifypid wie die aktuelle Seite
        $uri = $this->uriBuilder->reset()
            ->setTargetPageUid($verifypid)
            ->uriFor(
                $actionName = 'verify',
                $controllerArguments = ['token' => $standardForm->getToken()],
                $controllerName = 'DoubleOptinForm'
            );

        $standardForm->setVerificationUrl('###baseUrl###/' . $uri);

        //  set expiry date
        $standardForm->setValidUntil(strtotime("+24 hour"));

        //  set identifier
        $standardForm->setIdentifier('gem-community');  //  @todo: Das muss dynamisch sein und ggfs. aus der Flexform kommen!
        /*
        if (!$standardForm->getBstAgree()) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'bstFormController.error.accept_agree', 'rkw_form'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, array('standardForm' => $standardForm));
            //===
        }
        */

        parent::createAbstractAction($standardForm, $privacy);
    }

    /**
     * action verify
     * @param string $token
     * @return void
     */
    public function verifyAction(string $token = '')
    {

        // set objects if they haven't been injected yet
        if (!$this->objectManager) {
            $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        }
        if (!$this->persistenceManager) {
            $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);
        }

        if ($token) {

            DebuggerUtility::var_dump($token);

            //  @todo: Check, ob evtl. bereits enabled auf true gesetzt wurde. Dann entsprechend eine Info ausgeben.
            $result = $this->doubleOptinFormRepository->findByToken($token);

            if ($result) {
                //  set enabled
                $result->setEnabled(true);

                //  persist it
                $this->doubleOptinFormRepository->update($result);
                $this->persistenceManager->persistAll();

                //  oder direkt löschen, nachdem die Bestätigung rausgeschickt wurde

                //  @todo: E-Mails anpassen und schicken
                $this->mailHandling($result);
                exit();

                //  @todo: Mit Bestätigung weiterleiten

//                if (!$standardForm->getBstAgree()) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'bstFormController.error.accept_agree', 'rkw_form'
                        ),
                        '',
                        AbstractMessage::OK
                    );
                    $this->forward('new', null, null, array('standardForm' => null));
                    //===
//                }

            } else {
                DebuggerUtility::var_dump('No result found.');
            }

        }

    }

}
