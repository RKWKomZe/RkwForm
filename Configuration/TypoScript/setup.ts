config.tx_extbase.persistence {

    classes {

        #===============================================

        RKW\RkwRegistration\Domain\Model\FrontendUser {
            subclasses {
                Tx_RkwForm_FrontendUser = RKW\RkwForm\Domain\Model\FrontendUser
            }
        }

        RKW\RkwForm\Domain\Model\FrontendUser {
            mapping {
                tableName = fe_users
                recordType =
            }
        }

        #===============================================

        TYPO3\CMS\Extbase\Domain\Model\BackendUser {
            subclasses {
                Tx_RkwForm_BackendUser = RKW\RkwForm\Domain\Model\BackendUser
            }
        }


        RKW\RkwForm\Domain\Model\BackendUser {
            mapping {
                tableName = be_users
                columns {
                    usergroup.mapOnProperty = backendUserGroups
                }
            }
        }
    }
}

plugin.tx_rkwform {
    view {
        templateRootPaths.0 = EXT:rkw_form/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_rkwform.view.templateRootPath}
        partialRootPaths.0 = EXT:rkw_form/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_rkwform.view.partialRootPath}
        layoutRootPaths.0 = EXT:rkw_form/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_rkwform.view.layoutRootPath}
    }
    persistence {
        storagePid = {$plugin.tx_rkwform.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
    settings {

        mandatoryFields {
            standard = {$plugin.tx_rkwform.settings.mandatoryFields.standard}
        }
    }
}

# FormFramework conf (tx_form)
[userFunc = TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('form')]
    plugin.tx_form.settings.yamlConfigurations.100 = EXT:rkw_form/Configuration/Yaml/FormFramework/FormFrameworkConf.yaml
    module.tx_form.settings.yamlConfigurations.100 = EXT:rkw_form/Configuration/Yaml/FormFramework/FormFrameworkConf.yaml
[global]