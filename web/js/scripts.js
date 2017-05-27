$(document).ready(function () {
    spf.init();

    $('a.spf-link').click(function () {
        $('.button-collapse').sideNav('hide');
    })
    
    $('.button-collapse').sideNav();
    $('.collapsible').collapsible();
    $('.parallax').parallax();
    $('ul.tabs').tabs();
    $('input#search').focus(function() { $(this).parent().parent().addClass('focused'); });
    $('input#search').blur(function() {
      if (!$(this).val()) {
        $(this).parent().parent().removeClass('focused');
      }
    });
    $('.side-nav [data-url]').click(function () {
        window.location.href = $(this).data('url');
    })
})