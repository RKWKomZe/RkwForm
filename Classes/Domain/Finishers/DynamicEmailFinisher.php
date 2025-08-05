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
                    $name = 'Altida Maralushaj';
                    break;
                case 'by':
                    $email = 'wissinger@rkwbayern.de';
                    $name = 'Josef Wissinger';
                    break;
                case 'be':
                    $email = 'ebochenek@rkw-sachsen.de';
                    $name = 'Eva-Maria Bochenek';
                    break;
                case 'bb':
                    $email = 'ebochenek@rkw-sachsen.de';
                    $name = 'Eva-Maria Bochenek';
                    break;
                case 'hb':
                    $email = 'j.ferber@rkw-bremen.de';
                    $name = 'Jennifer Ferber';
                    break;
                case 'hh':
                    $email = 'grund@rkw-nord.de';
                    $name = 'Ernst Grund';
                    break;
                case 'he':
                    $email = 't.fabich@rkw-hessen.de';
                    $name = 'Thomas Fabich';
                    break;
                case 'mv':
                    $email = 'grund@rkw-nord.de';
                    $name = 'Ernst Grund';
                    break;
                case 'ni':
                    $email = 'grund@rkw-nord.de';
                    $name = 'Ernst Grund';
                    break;
                case 'nw':
                    $email = 'nrw-verein@rkw.de';
                    $name = 'Dr. Andreas Blaeser-Benfer';
                    break;
                case 'rp':
                    $email = 'geschaeftsstelle@rkw-rlp.de';
                    $name = 'Jürgen Behrens';
                    break;
                case 'sl':
                    $email = 'christoph.esser@saaris.saarland';
                    $name = 'Dr. Christoph Esser';
                    break;
                case 'sn':
                    $email = 'ebochenek@rkw-sachsen.de';
                    $name = 'Eva-Marie Bochenek';
                    break;
                #case 'st':
                #    $email = 'TODO@rkw-sachsen-anhalt.de';
                #    $name = 'RKW Sachsen-Anhalt';
                #    break;
                case 'sh':
                    $email = 'grund@rkw-nord.de';
                    $name = 'Ernst Grund';
                    break;
                case 'th':
                    $email = 'kluge@rkw-thueringen.de';
                    $name = 'Annika Kluge';
                    break;
            }
        }

        // Beide dynamischen Felder hinzufügen
        $formRuntime->offsetSet('dynamicEmailAddress', $email);
        $formRuntime->offsetSet('dynamicEmailName', $name);

        return '';
    }
}
