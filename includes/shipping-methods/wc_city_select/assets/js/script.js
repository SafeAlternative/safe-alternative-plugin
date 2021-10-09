(function ($) {
  let selected_locker_id = null;
  const INPUTS = "input[name=payment_method], select[name=billing_city], select[name=shipping_city], select[name=billing_state], select[name=shipping_state], input[name=shipping_city], input[name=billing_postcode], input[name=shipping_postcode]";
  const hideNullPrice = (selector) => ($(selector).text() == "0.00 lei") ? $(selector).hide() : $(selector).show();

  $(document).ready(() => {
    $('body').on("change", INPUTS, () => $('body').trigger('update_checkout'));
    $('body').on('change', '#safealternative_fan_collectpoint_select, #safealternative_sameday_lockers_select', e => selected_locker_id = $(e.currentTarget).val());
    $('body').on('change', 'input[name*=shipping_method]', () => selected_locker_id = null);
    $('.state_select:visible').trigger('change');
    if ($('body').hasClass('woocommerce-cart'))
      $('.woocommerce-cart .woocommerce-Price-amount').each(function () { hideNullPrice(this) })
  });

  $(document).ajaxComplete((event, xhr, settings) => {
    if (settings.url.includes("wc-ajax=update_order_review") || settings.url.includes("wc-ajax=get_refreshed_fragments")) {
      $('.woocommerce-shipping-methods .woocommerce-Price-amount').each(function () { hideNullPrice(this) });
      if (selected_locker_id != null)
        $('#safealternative_fan_collectpoint_select, #safealternative_sameday_lockers_select').val(selected_locker_id).trigger('change');
    }
  });
})($ = jQuery);