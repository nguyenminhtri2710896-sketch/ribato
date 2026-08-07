//====================SELECT2 INIT=================//

function closeSelect () {
  $('.select-2').select2('close');
}

$(document).ready(function () {
  $(document).on('focus', '.select2.select2-container', function (e) {
    // only open on original attempt - close focus event should not fire open
    if (e.originalEvent && $(this).find(".select2-selection--single").length > 0) {
      $(this).siblings('select:enabled').select2('open')
    }
  });
  var select2Timeout;
  if ($(window).width() <= 767) {
    if ($('body .select-2').hasClass('select-2')) {
      $('body').append('<div class="select-2-backdrop"></div>')
    }
  }
  $('.select-2-backdrop').on('click', function () {
    $('.select-2').select2('close');
  })
  $('.select-2:not([multiple])').select2({
    width: '100%',
    closeOnSelect: $(window).width() < 768 ? false : true,
  }).on("select2:opening", function () {
    if ($(window).width() <= 767) {
      clearTimeout(select2Timeout);
    }
  }).on("select2:open", function () {

      $(this).parents('.form-group').addClass('input-focus')
      $(".select2-search--dropdown .select2-search__field").attr("placeholder", $('.select-2').attr('placeholder'));
    if ($(window).width() >= 789) {
      if (navigator.userAgent.indexOf('MSIE') !== -1
        || navigator.appVersion.indexOf('Trident/') > -1) {
        window.setTimeout(function () {
          document.querySelector('.select2-search input').blur()
        }, 0);
      }
    }
    if ($(window).width() < 789) {
      window.setTimeout(function () {
        document.querySelector('.select2-search input').blur()
      }, 0);
    }
    if ($(window).width() <= 767) {
      clearTimeout(select2Timeout);
      console.log($(this))
      BNS.on()
      $('.close-select').remove();
      // var x = $(this).eq(0).attr('header-text');
      $('body > .select2-container .select2-dropdown').prepend('<div class="close-select"><div class="close-select__btn" href="javascript:void(0)" onclick="closeSelect()"></div></div>');
      $('body > .select2-container .select2-dropdown').addClass('top-0');
      $('.select-2-backdrop').addClass('show');
    }
  }).on("select2:closing", function () {
    $('.select-2-backdrop').removeClass('show');
    if ($(window).width() <= 767) {
      BNS.off()
      $('body > .select2-container .select2-dropdown').removeClass('top-0');
    }
  }).on("select2:select", function () {
    if ($(window).width() <= 767) {
      $('.select-2-backdrop').removeClass('show');
      $('body > .select2-container .select2-dropdown').removeClass('top-0');
      select2Timeout = setTimeout(function () {
        $('.select-2').select2('close');
      }, 320)
    }
  }).on("select2:close", function () {
    $(this).parents('.form-group').removeClass('input-focus')
  });

  //template select-2
  function templateImg (img) {
    if (!img.id) { return img.text; }
    if (img.element.attributes.dataimgClass) {
      return $img = $('<div class="select-tpl-img-wrap"><div class="row row-10 align-items-center"><div class="col-auto"><div class="select-tpl-img ' + img.element.attributes.dataimgClass.value + '" style="background-image: url(\'' + img.element.attributes.dataimg.value + '\')"></div></div><div class="col text-truncate"><div class="select-tpl-txt">' + img.text + '</div></div></div></div>');
    } else {
      return $img = $('<div class="select-tpl-img-wrap"><div class="row row-10 align-items-center"><div class="col text-truncate"><div class="select-tpl-txt">' + img.text + '</div></div></div></div>');
    }
  };
  function templateImgRes (img) {
    if (!img.id) { return img.text; }
    if (img.element.attributes.dataimgClass) {
      return $img = $('<div class="select-tpl-img-wrap"><div class="row row-10 align-items-center"><div class="col-auto"><div class="select-tpl-img ' + img.element.attributes.dataimgClass.value + '" style="background-image: url(\'' + img.element.attributes.dataimg.value + '\')"></div></div><div class="col"><div class="select-tpl-txt ">' + img.text + '</div></div></div></div>');
    } else {
      return $img = $('<div class="select-tpl-img-wrap"><div class="row row-10 align-items-center"><div class="col"><div class="select-tpl-txt">' + img.text + '</div></div></div></div>');
    }
  };
  $('.select-2-template').select2({
    templateResult: templateImgRes,
    templateSelection: templateImg,
    width: '100%'
  });
  $('.select-2-template[multiple]').select2({
    templateResult: templateImgRes,
    templateSelection: templateImg,
    closeOnSelect: false,
    width: '100%'
  });
  //end template select-2

});
//====================END SELECT2 INIT=================//