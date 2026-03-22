# CiviCRM: Receipt Invoice Attachment
## com.joineryhq.rcptinv

Increases coverage of the setting "Automatically email invoice when user purchases
online".

In CiviCRM, this setting (under Administer > CiviContribute > CiviContribute
Component Settings) will cause an invoice PDF to be attached to receipt emails
in many cases, but not has no such effect in some cases.

This extension extends this setting by causing the invoice PDF to be attached in
these additional situations:

* Upon saving edits to a participant record with then "Send Confirmation and
  Receipt" checkbox selected. Admittedly, CiviCRM core is already attaching
  this in some cases; but this extension ensures it's attached in all such cases.
* Upon submitting a payment ('Submit Payment' or 'Submit Credit Card Payment')
  on an exisgin (e.g. pending) contribution.

Before adding the attachment, this extension attempts to verify that an attachment
named "Invoice.pdf" does not already exist on the email.


## License
This is an [extension for CiviCRM](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/),
licensed under [GPL-3.0](LICENSE.txt).

## Support

Support for this package is handled under Joinery's ["As-Is Support" policy](https://joineryhq.com/software-support-levels#as-is-support).

Public issue queue for this package: https://github.com/JoineryHQ/com.joineryhq.rcptinv/issues
