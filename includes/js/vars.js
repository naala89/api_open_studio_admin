/**
 * JS for the admin vars page.
 *
 * @package   ApiOpenStudioAdmin
 * @license   This Source Code Form is subject to the terms of the
 *            Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://www.apiopenstudio.com/license/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 Naala Pty Ltd
 * @link      https://www.apiopenstudio.com
 */

/* global M, APIOPENSTUDIO */

$(document).ready(function () {
  $('#modal-var-create select[name="create-var-accid"]')
    .on('change', function () {
      APIOPENSTUDIO.setApplicationOptions(
        $(this).val(),
        '#modal-var-create select[name="create-var-appid"]'
      )
    })

  $('#modal-var-create select[name="create-var-appid"]')
    .on('change', function () {
      APIOPENSTUDIO.setAccount(
        $(this).val(),
        '#modal-var-create select[name="create-var-accid"]'
      )
    })

  $('.modal-var-edit-trigger').on('click', function () {
    const self = $(this)
    const modal = $('#modal-var-edit')
    modal.find('input[name="edit-var-vid"]').val(self.attr('vid'))
    modal.find('.edit-var-key').html(self.attr('key'))
    modal.find('textarea[name="edit-var-val"]').val(self.attr('val'))
    M.textareaAutoResize()
    M.updateTextFields()
  })

  $('.modal-var-delete-trigger').on('click', function () {
    const self = $(this)
    const modal = $('#modal-var-delete')
    modal.find('input[name="delete-var-vid"]').val(self.attr('vid'))
    modal.find('.delete-var-key').html(self.attr('key'))
  })
})
