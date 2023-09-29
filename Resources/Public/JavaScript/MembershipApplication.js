
function MembershipApplication() {
  let _this = this;

  this.settings = {

    grossTotalClass: '.membership-fee-gross-total input.form-control',
    grossTotalYearClass: '.membership-fee-gross-total-year input.form-control',
    feeYearlyClass: '.membership-fee-yearly input.form-control',
    foundingDateInputClass: '.membership-founding-date input.form-control',
    feeYearlyValue: 0,
    $grossTotalInput: null,
    $grossTotalYearInput: null,
    $feeYearlyInput: null,
    $foundingDateInput: null,
    scale: [
      {
        min: 0,
        max: 200000,
        fee: 102
      },
      {
        min: 200000,
        max: 1000000,
        fee: 128
      },
      {
        min: 1000000,
        max: 2000000,
        fee: 153
      },
      {
        min: 2000000,
        max: 3000000,
        fee: 179
      },
      {
        min: 3000000,
        max: 4000000,
        fee: 205
      },
      {
        min: 4000000,
        max: 5000000,
        fee: 230
      },
      {
        min: 5000000,
        max: 6000000,
        fee: 256
      },
      {
        min: 6000000,
        max: 8000000,
        fee: 281
      },
      {
        min: 8000000,
        max: 10000000,
        fee: 307
      },
      {
        min: 10000000,
        max: 12000000,
        fee: 332
      },
      {
        min: 12000000,
        max: 14000000,
        fee: 383
      },
      {
        min: 14000000,
        max: 16000000,
        fee: 435
      },
      {
        min: 16000000,
        max: 20000000,
        fee: 511
      },
      {
        min: 20000000,
        max: 31000000,
        fee: 614
      },
      {
        min: 31000000,
        max: 41000000,
        fee: 716
      },
      {
        min: 41000000,
        max: 61000000,
        fee: 869
      },
      {
        min: 61000000,
        max: 100000000,
        fee: 1023
      }

    ]
  };

  this.init = function () {

    _this.settings.$grossTotalInput = jQuery(_this.settings.grossTotalClass).first();
    _this.settings.$grossTotalYearInput = jQuery(_this.settings.grossTotalYearClass).first();
    _this.settings.$feeYearlyInput = jQuery(_this.settings.feeYearlyClass).first();
    _this.settings.$foundingDateInput = jQuery(_this.settings.foundingDateInputClass).first();

    jQuery(_this.settings.$grossTotalInput).on('change', function() {
      _this.calculateFee();
    });

    jQuery(_this.settings.$foundingDateInput).on('change', function() {
      _this.calculateFee();
    });

    jQuery(_this.settings.$grossTotalYearInput).on('change', function() {
      _this.calculateFee();
    });

  };

  this.calculateFee = function(event) {

    let foundingDateRaw = _this.settings.$foundingDateInput.val().split('.');
    let foundingDate = new Date(Date.parse(foundingDateRaw[2] + '-' + foundingDateRaw[1] + '-' + foundingDateRaw[0]));
    let now = new Date();

    if ((now.getTime() - foundingDate.getTime()) < (60 * 60 * 24 * 365 * 1000)) {
      _this.settings.$feeYearlyInput.val(0);
    } else if (foundingDate.getFullYear() > now.getFullYear() - 2) {
      _this.settings.$feeYearlyInput.val(60);
    } else {
      _this.settings.scale.forEach(_this.isInRange);
      _this.settings.$feeYearlyInput.val(_this.settings.feeYearlyValue);
    }

  };

  this.isInRange = function(item) {

    if (
      _this.settings.$grossTotalInput.val() >= item.min
      &&
      _this.settings.$grossTotalInput.val() <= item.max
    ) {
      _this.settings.feeYearlyValue = item.fee;
    }
  }

}

jQuery(document).ready(function() {

  if (jQuery('[id^=mitgliedsantrag-sa]').length > 0) {
    let membershipApplication = new MembershipApplication();
    membershipApplication.init();
  }

});
