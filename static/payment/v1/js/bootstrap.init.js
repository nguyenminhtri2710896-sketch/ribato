//====================BOOTSTRAP INIT=================//

// fix modal
// end fix modal
$(document).ready(function () {

  var modalEls = document.querySelectorAll('.modal')
  if (modalEls) {
    modalEls.forEach(el => {
      el.addEventListener('shown.bs.modal', function (event) {
        window.innerWidth < 769 ? BNS.on() : ''
      })
      el.addEventListener('hide.bs.modal', function (event) {
        window.innerWidth < 769 ? BNS.off() : ''
      })
    })
  }



  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-tooltip="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl, {
      html: true,
      popperConfig: {
        modifiers: {
          preventOverflow: {
            boundariesElement: 'viewport',
            escapeWithReference: true
          }
        },
      }
    })
  })
});
//====================END BOOTSTRAP INIT=================//