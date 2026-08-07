//====================CLEAVE INIT=================//
$(document).ready(function () {
  //Cleave
  if (('.cleave-mm-yy').length) {
    $('.cleave-mm-yy').toArray().forEach(function (field) {
      new Cleave(field, {
          date: true,
          dateMin: minDate.toISOString().substr(2, 5),
          dateMax: maxDate.toISOString().substr(2, 5),
        datePattern: ['m', 'y']
      });
    })
  }
});
//====================END CLEAVE INIT=================//