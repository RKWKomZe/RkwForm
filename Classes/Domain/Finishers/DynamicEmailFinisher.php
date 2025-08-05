<?php

namespace RKW\RkwForm\Domain\Finishers;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

class DynamicEmailFinisher extends AbstractFinisher
{
    /**
     * Executes this finisher
     */
    protected function executeInternal(): string
    {
        // Zugriff auf das Formular und die bisher eingegebenen Werte
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formValues = $formRuntime->getFormState()->getFormValues();

        // Standardwerte (Fallback)
        $email = 'service@mein.rkw.de';
        $name = 'RKW Service';

        if (!empty($formValues['bundesland'])) {
            switch ($formValues['bundesland']) {
                case 'bw':
                    $email = 'maralushaj@rkw-bw.de';
                    $name = 'Mara Lushaj';
                    break;
                case 'by':
                    $email = 'wissinger@rkwbayern.de';
                    $name = 'Herr/Frau Wissinger';
                    break;
                case 'be':
                    $email = 'ebochenek@rkw-sachsen.de';
                    $name = 'E. Bochenek';
                    break;
                case 'bb':
                    $email = 'ebochenek@rkw-sachsen.de';
                    $name = 'E. Bochenek';
                    break;
                case 'hb':
                    $email = 'j.ferber@rkw-bremen.de';
                    $name = 'J. Ferber';
                    break;
                case 'hh':
                    $email = 'grund@rkw-nord.de';
                    $name = 'RKW Nord Hamburg';
                    break;
                case 'he':
                    $email = 't.fabich@rkw-hessen.de';
                    $name = 'T. Fabich';
                    break;
                case 'mv':
                    $email = 'grund@rkw-nord.de';
                    $name = 'RKW Nord Mecklenburg-Vorpommern';
                    break;
                case 'ni':
                    $email = 'grund@rkw-nord.de';
                    $name = 'RKW Nord Niedersachsen';
                    break;
                case 'nw':
                    $email = 'nrw-verein@rkw.de';
                    $name = 'RKW NRW Verein';
                    break;
                case 'rp':
                    $email = 'geschaeftsstelle@rkw-rlp.de';
                    $name = 'RKW Rheinland-Pfalz';
                    break;
                case 'sl':
                    $email = 'christoph.esser@saaris.saarland';
                    $name = 'Christoph Esser';
                    break;
                case 'sn':
                    $email = 'ebochenek@rkw-sachsen.de';
                    $name = 'E. Bochenek';
                    break;
                #case 'st':
                #    $email = 'TODO@rkw-sachsen-anhalt.de';
                #    $name = 'RKW Sachsen-Anhalt';
                #    break;
                case 'sh':
                    $email = 'grund@rkw-nord.de';
                    $name = 'RKW Nord Schleswig-Holstein';
                    break;
                case 'th':
                    $email = 'kluge@rkw-thueringen.de';
                    $name = 'Kluge - RKW Thüringen';
                    break;
            }
        }

        // Beide dynamischen Felder hinzufügen
        $formRuntime->offsetSet('dynamicEmailAddress', $email);
        $formRuntime->offsetSet('dynamicEmailName', $name);

        return '';
    }
}
