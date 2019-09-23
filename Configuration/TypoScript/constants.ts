
plugin.tx_rkwform {
    view {
        # cat=plugin.tx_rkwform/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:rkw_form/Resources/Private/Templates/
        # cat=plugin.tx_rkwform/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:rkw_form/Resources/Private/Partials/
        # cat=plugin.tx_rkwform/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:rkw_form/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_rkwform//a; type=string; label=Default storage PID
    storagePid =

    }
    settings {
        mandatoryFields {
            # cat=plugin.tx_rkwform//f; type=string; label=mandatory fields for standard form (e.g. salutation, firstName, lastName, company, email, phone, text )
            standard = firstName, lastName, email, salutation, text
        }

        mail {
            # cat=plugin.tx_rkwform//f; type=string; label=comma-separated list of backend user-uids (fallback)
            fallbackBackendUser =
        }
    }
}
