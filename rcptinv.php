<?php

require_once 'rcptinv.civix.php';

use CRM_Rcptinv_ExtensionUtil as E;

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess/
 */
function rcptinv_civicrm_preProcess($formName, $form) {
  if (
    $formName == 'CRM_Contribute_Form_AdditionalPayment'
    && $form->_flagSubmitted
  ) {
    // In the workflow of this form ('submit payment' or 'submit credit card payment')
    // the contribution_id is not available in scope for hook_civicrm_alterMailParams.
    // To make it available there, we store it in the static var 'rcptinv_contribution_id'.
    \Civi::$statics['rcptinv_contribution_id'] = $formValues = $form->getVar('_contributionId');
  }
}

/**
 * Implements hook_civicrm_alterMailParams().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterMailParams/
 */
function rcptinv_civicrm_alterMailParams(&$params, $context) {
  if ($context == 'singleEmail' && Civi::settings()->get('invoice_is_email_pdf')) {
    switch ($params['workflow']) {
      case 'event_offline_receipt':
        if ($params['tplParams']['participantID'] ?? FALSE) {
          $contactId = $params['contactId'];
          $participantPaymentGet = civicrm_api3('participantPayment', 'get', ['participant_id' => $params['tplParams']['participantID']]);
          $participantPayment = reset($participantPaymentGet['values']);
          $contributionId = ($participantPayment['contribution_id'] ?? NULL);
        }
        break;

      case 'payment_or_refund_notification':
        $contactId = $params['contactId'];
        $contributionId = $params['tplParams']['contributionID'];
        if (!$contributionId) {
          // In the workflow of 'submit payment' or 'submit credit card payment'
          // on an existing contribution, the contribution ID is not available
          // in $params. In this workflow, we've set the static var 'rcptinv_contribution_id'
          // in hook_civicrm_preProcess() for the form 'CRM_Contribute_Form_AdditionalPayment'
          $contributionId = \Civi::$statics['rcptinv_contribution_id'];
        }
        break;

    }
    if ($contactId && $contributionId) {
      $hasInvoiceAttachment = FALSE;
      foreach ($params['attachments'] as $attachment) {
        if (($attachment['cleanName'] ?? NULL) == 'Invoice.pdf') {
          $hasInvoiceAttachment = TRUE;
          break;
        }
      }
      if (!$hasInvoiceAttachment) {
        $html = CRM_Contribute_BAO_ContributionPage::addInvoicePdfToEmail($contributionId, $contactId);
        $pdfFormat = CRM_Core_BAO_MessageTemplate::getPDFFormatForTemplate('contribution_invoice_receipt');
        $attachment = CRM_Utils_Mail::appendPDF('Invoice.pdf', $html, $pdfFormat);
        $attachment['fileName'] = $fileName;
        $params['attachments'][] = $attachment;
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function rcptinv_civicrm_config(&$config): void {
  _rcptinv_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function rcptinv_civicrm_install(): void {
  _rcptinv_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function rcptinv_civicrm_enable(): void {
  _rcptinv_civix_civicrm_enable();
}
