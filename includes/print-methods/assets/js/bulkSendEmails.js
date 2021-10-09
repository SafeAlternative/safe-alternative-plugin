jQuery(function ($) {
  $('<option>').val('bulkSendEmails').text('Trimite email cu AWB-ul generat').appendTo("select[name='action'], select[name='action2']");

  $('#doaction, #doaction2').click(function (e) {
    let select = $(this).siblings('select');

    if (select.val() === 'bulkSendEmails') {
      e.preventDefault();
      let checked_inputs = $('input[name="post[]"]:checked'),
        order_ids = checked_inputs.serializeArray();

      $.ajax({
        type: "POST",
        dataType: "json",
        url: ajaxurl,
        data: {
          action: "safealternative_send_awb_email",
          order_ids
        },
        beforeSend: function() {
          let location = $('.wp-header-end');
          location.after(`<div class="notice notice-warning is-dismissible bulkSendEmailsNotice"><p>Emailuri de notificare cu AWB-urile generate sunt in curs de trimitere. Va rugam asteptati.</p></div>`)
        },
        success: function (response) {
          let notice = $('.bulkSendEmailsNotice').removeClass('notice-warning').addClass('notice-success').html('<p>Emailurile au fost trimise.</p>');
          setTimeout(function () { notice.fadeOut(750) }, 5000);
        }
      })
    }
  });
});