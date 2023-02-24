# rkw_form

## What does it do?
It's sole purpose is form management.
On the one hand, it's provides simple forms without a bigger context (Analog to the Ext FormFramework).
On the other hand, it's prepared to extend the FormFramework in future with some special functions.

## ExtForm Framework base configuration
* Needs upload folder fileadmin/user_upload/tx_rkwform
* Do not edit the forms in live context via backend. Would be overwritten with every following deployment, because the form definitions are lying in /rkw_form/Configuration/Yaml/Forms/
* The whole FormExt form we can work with and extend it: https://docs.typo3.org/c/typo3/cms-form/master/en-us/I/Config/configuration/Index.html
### Available cronjob
* Because the "Delete uploads" mail finisher only clear uploads of successfully sent forms, we need a cronjob to clear the upload folder
* Cronjob name: "RkwForm: cleanup"
* Argument: "daysFromNow" - Defines which old files should be deleted. Default value: 30 (days)
* If no htaccess protection for the given upload folder exists, the cronjob should create one. Take a look to the CommandController function "securityCheck"
* Change or add the upload destination here inside the FormFrameworkConf.yaml: TYPO3.CMS.Form.persistenceManager.allowedFileMounts
* Intentional restriction: The cronjob handles multiple filemounts. Condition: The filepath must contain the string "tx_rkwform"
### Incorporated changes / improvements of the basic ExtForm
* HTML5 validation deactivated (no styles available)
* Replacing the bootstrap based "ViewGrid" for every field with simple width property
* Individual form error messages
* h1-h6 options for static text field
* The checkbox field got the property "type" to use it as terms-checkbox. Is bound to the extension RkwRegistration
* Individual greeting text for every E-Mails (look to the mail finisher of the certain form)
* The mailing is bound to the RkwMailer for using Header, Footer and its components (images; links) for every E-Mail out of the box
* Attention: The EmailFinisher of the ExtForm is overwritten by /rkw_form/Classes/Domain/Finishers/EmailFinisher.php
* Hint: To work in fluid with individual form configurations you can use a YAML-reader like it is used here: /rkw_form/Classes/ViewHelpers/GetFinisherOptionViewHelper.php
### Hints
* For FE (fluid) translation use "locallang.xlf"
* For BE (yaml) translation use "form_framework.xlf"
* To understand how to edit the basics of all FormExt forms itself or of it's fields, take a look to the FormFrameworkConf.yaml file. This file defines all BASICS
* To edit a special form you want to display as content element, you should use the backend module "Forms". But you can also directly change it via /rkw_forms/configuration/Yaml/Forms/YourForm
* Hint: Directly changed form definitions in yaml can be overwritten directly on saving the given form in the TYPO3 backend

## Add new forms (not using FormFramework)
* All you need to know: One form is one plugin. Show following content only if you need further instructions

### Create new form WITHOUT extend the form database table (show extended using below)
* ext_localconf: Create the new form plugin. Further down we're creating also the necessary "ExampleController"
```
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'RKW.RkwForm',
    'ExampleForm',
    [
        'ExampleForm' => 'new, create'
    ],
    // non-cacheable actions
    [
        'ExampleForm' => 'new, create'
    ]
);
```
* TCA/override/tt_content: Register the new form plugin
```
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'RKW.RkwForm',
    'ExampleForm',
    'RKW Form: Example Formular'
);
```
* TCA/override/tt_content: Use the standard flexform. Just add your new plugin name to the array
```
$pluginList = ['StandardForm', 'ExampleForm'];
```
* Controller: Create a new controller which extends the AbstractFormController (for possible individual purpose)
```
class ExampleFormController extends \RKW\RkwForm\Controller\AbstractFormController
```
* Controller: Add the createAction and call the parents "createAbstractAction", because the AbstractController can't handle the individual form contents directly by using the AbstractEntity (missing getter & setter)
```
/**
 * action create
 *
 * @param \RKW\RkwForm\Domain\Model\StandardForm $standardForm
 * @param int $privacy
 * @TYPO3\CMS\Extbase\Annotation\Validate("\RKW\RkwForm\Validation\Validator\AbstractFormValidator", param="standardForm")
 * @return void
 */
public function createAction(BstForm $standardForm, $privacy = 0): void
{
    // my example code, which is different to the createActractAction of the AbstractController

    parent::createAbstractAction($standardForm, $privacy);
}
```
* TypoScript: Define your plugins individual mandatory fields (in CONSTANTS & SETUP)
* HINT: SK has changed the logic. You have to create an own validator for every form
```
// ####################
// ExampleForm
// ####################
plugin.tx_rkwform_exampleform < plugin.tx_rkwform
plugin.tx_rkwform_exampleform {
    settings {
        mandatoryFields {
            standard = {$plugin.tx_rkwform_exampleform.settings.mandatoryFields.standard}
        }
    }
}
```
* Resources: Create you individual templates and partials
* Validation: You have NEVER need to rewrite the AbstractFormValidator, until you have some special needs
* Mailing: You have NEVER need to overwrite, extend oder change the PHP-based mail parts, until you have some special needs


### EXTENDED: If you need to extend the form table
* ext_tables.sql: Extend form fields, if necessary. Please check before if the standard form fields are already enough
```
#
# extend for exampleForm
#
CREATE TABLE tx_rkwform_domain_model_standardform (
  exmpl1 int(11) DEFAULT '0' NOT NULL,
  exmpl2 int(11) DEFAULT '0' NOT NULL,
  exmpl3 int(11) DEFAULT '0' NOT NULL,
  exmpl4 tinyint(4) unsigned DEFAULT '0' NOT NULL,
);
```
* TCA: Add contents by overriding TCA/Overrides/tx_rkwform_domain_model_standardform
```
class ExampleForm extends \RKW\RkwForm\Domain\Model\StandardForm
```
* Model: Create Model
```
class ExampleForm extends \RKW\RkwForm\Domain\Model\StandardForm
```
* Repository: Create Repository
```
class ExampleFormRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
```
* Locallang: Create all necessary language contents (also form errors!!)
* BE
```
<!-- example extend -->
<trans-unit id="tx_rkwform_domain_model_standardform.exmpl1">
    <source>Number 1</source>
    <target>Pers. Zahlenkomb. 1</target>
</trans-unit>
<trans-unit id="tx_rkwform_domain_model_standardform.exmpl2">
    <source>Number 2</source>
    <target>Pers. Zahlenkomb. 2</target>
</trans-unit>
<trans-unit id="tx_rkwform_domain_model_standardform.exmpl3">
    <source>Number 3</source>
    <target>Pers. Zahlenkomb. 3</target>
</trans-unit>
<trans-unit id="tx_rkwform_domain_model_standardform.exmpl4">
    <source>Agree</source>
    <target>Zustimmung </target>
</trans-unit>

<!-- example extend -->
<trans-unit id="tx_rkwform_domain_model_standardform.tabs.bst">
    <source>Example</source>
    <target>Beispiel</target>
</trans-unit>
<trans-unit id="tx_rkwform_domain_model_standardform.palettes.bst">
    <source>Example</source>
    <target>Beispiel</target>
</trans-unit>
```
* FE
```
<trans-unit id="form.error.newFormRequest.exmpl1">
    <source>Number combination 1</source>
    <target>Zahlenkombination Feld 1</target>
</trans-unit>
<trans-unit id="form.error.newFormRequest.exmpl2">
    <source>Number combination 2</source>
    <target>Zahlenkombination Feld 2</target>
</trans-unit>
<trans-unit id="form.error.newFormRequest.exmpl3">
    <source>Number combination 3</source>
    <target>Zahlenkombination Feld 3</target>
</trans-unit>
<trans-unit id="form.error.newFormRequest.exmpl4">
    <source>Message</source>
    <target>Nachricht</target>
</trans-unit>

```
* E-Mail: Keep in mind, that this form ext ist mainly working with and trough emails. So add your new fields to /ext/rkw_form/Resources/Private/Partials/Email/Details.html
