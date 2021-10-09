jQuery(function ($) {
  $('<option>').val('generateAWB_Sameday').text('Genereaza AWB Sameday').appendTo("select[name='action'], select[name='action2']");

  $('#doaction, #doaction2').click(function (e) {
    let select = $(this).siblings('select');

    if (select.val() === 'generateAWB_Sameday') {
      e.preventDefault();
      var urls = [];
      let checked_inputs = $('input[name="post[]"]:checked');

      checked_inputs.each(function (key, element) {
        let generateAWBbtn = $(element).parents('th').siblings('td.Sameday_AWB');
        let url = generateAWBbtn.find('a.generateBtn').attr('href');

        if (undefined !== url) {
          generateAWBbtn.find('img').attr('src', '/wp-admin/images/spinner.gif');
          generateAWBbtn.attr('disabled', 'disabled');
          generateAWBbtn.css('pointer-events', 'none');
          urls.push(window.location.origin + url);
        }
      });

      var activeAjaxConnections = 0;
      $.each(urls, function (i, url) {
        setTimeout(function () {
          $.ajax(
            url,
            {
              beforeSend: function (xhr) {
                activeAjaxConnections++;
              },
              type: 'GET',
              success: function (data) {
                activeAjaxConnections--;
                if (0 == activeAjaxConnections) {
                  window.location.reload();
                }
              },
              error: function () {
                activeAjaxConnections--;
                if (0 == activeAjaxConnections) {
                  window.location.reload();
                }
              }
            }
          );
        }, i * 1000);

      });
    }
  });
});