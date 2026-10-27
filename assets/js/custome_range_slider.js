$(function() {
  var $min = $("#minsalary");
  var $max = $("#maxsalary");
  var maxsalary = $("#maxsalary").val();
  var $range = $("#rangesalary");

  $range.slider({
    range: true,
    min: 0,
    max: 10000000,
    values: [0, 10000000],
    slide: function(event, ui) {
      $min.val(ui.values[0]);
      $max.val(ui.values[1]);
    }
  });
  $min.change(function() {
    var value1 = $min.val();
    var value2 = $max.val();

    if (parseInt(value1) > parseInt(value2)) {
      value1 = value2;
      $min.val(value1);
    }

    $range.slider("values", 0, value1);
  });

  $max.change(function() {
    var value1 = $min.val();
    var value2 = $max.val();

    if (value2 > 10000000) {
      value2 = 10000000;
      $max.val(10000000);
    }

    if (parseInt(value1) > parseInt(value2)) {
      value2 = value1;
      $max.val(value2);
    }

    $range.slider("values", 1, value2);
  });
});


$(function() {
  var $min = $("#min");
  var $max = $("#max");
  var $range = $("#range");

  $range.slider({
    range: true,
    min: 0,
    max: 100,
    values: [0, 100],
    slide: function(event, ui) {
      $min.val(ui.values[0]);
      $max.val(ui.values[1]);
    }
  });

  $min.change(function() {
    var value1 = $min.val();
    var value2 = $max.val();

    if (parseInt(value1) > parseInt(value2)) {
      value1 = value2;
      $min.val(value1);
    }

    $range.slider("values", 0, value1);
  });

  $max.change(function() {
    var value1 = $min.val();
    var value2 = $max.val();

    if (value2 > 100) {
      value2 = 100;
      $max.val(100);
    }

    if (parseInt(value1) > parseInt(value2)) {
      value2 = value1;
      $max.val(value2);
    }

    $range.slider("values", 1, value2);
  });
});
