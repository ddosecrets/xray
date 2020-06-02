var angle = 0;

function rotate(rotation) {
  var img = $('img').first();
  var parent = img.parent();
  angle = (angle + rotation) % 360;
  if (angle % 180) {
    parent.css('width', (parent.parent().width() - 10) * img.width() / img.height());
    img.css('position', 'relative');
    img.css('width', parent.width() - (img.width() > img.height() ? 10 : 0));
    img.css('top', (img.width() - img.height()) / 2);
    img.css('left', (img.height() - img.width()) / 2);
    parent.css('height', img.width() + 10);
  } else {
    img.css('position', '');
    img.css('width', '100%');
    img.css('width', 'calc(100% - 10px)');
    img.css('top', '');
    parent.css('height', '');
    parent.css('width', '');
  }
  img.rotate(angle);
}

function resize() {
  var height = $(window).height();
  $('.vertical-top, .vertical-bottom').height(height / 2);
  $('.horizontal-left, .horizontal-right').height(height);
}

function vertical() {
  $('img').parent().parent().height($(window).height() / 2 - 55);
  $('table').parent().height($(window).height() / 2 - 55);
  $('.btn-vertical').prop('disabled', true);
  $('.btn-horizontal').prop('disabled', false);
  $('.vertical-top').append($('.vertical-top, .horizontal-left').children().detach());
  $('.vertical-bottom').append($('.vertical-bottom, .horizontal-right').children().detach());
  $('.vertical-top, .vertical-bottom').show();
  $('.horizontal-left, .horizontal-right').hide();
  rotate(0);
  if (typeof history !== 'undefined' && typeof history.replaceState === 'function') {
    history.replaceState({}, '', location.pathname);
  } else {
    location.hash = '#';
  }
}

function horizontal() {
  $('img').parent().parent().height($(window).height() - 55);
  $('table').parent().height($(window).height() - 55);
  $('.btn-vertical').prop('disabled', false);
  $('.btn-horizontal').prop('disabled', true);
  $('.horizontal-left').append($('.vertical-top, .horizontal-left').children().detach());
  $('.horizontal-right').append($('.vertical-bottom, .horizontal-right').children().detach());
  $('.horizontal-left, .horizontal-right').show();
  $('.vertical-top, .vertical-bottom').hide();
  rotate(0);
  if (typeof history !== 'undefined' && typeof history.replaceState === 'function') {
    history.replaceState({}, '', '#wide');
  } else {
    location.hash = '#wide';
  }
}

function resize() {
  if (location.hash === '#wide') {
    horizontal();
  } else {
    vertical();
  }
}

resize();

$(window).on('resize', resize);

function next() {
  location.reload();
}

function save() {
  $('input, label, select').on('mousedown', false).css('cursor', 'not-allowed');
  $('input:radio:not(:checked)').prop('disabled', true);
  $('.btn-add, .btn-remove').prop('disabled', true);
  $('.btn-editing').hide();
  $('.btn-submitting').show();
  $('input[name*="date"]').datepicker('destroy');
}

function edit() {
  $('.data-fields').show().find('input').prop('disabled', false);
  $('input, label, select').off('mousedown').css('cursor', '');
  $('input:radio:not(:checked)').prop('disabled', false);
  $('.btn-add, .btn-remove').prop('disabled', false);
  $('.btn-editing').show();
  $('.btn-submitting').hide();
  $('input[name*="date"]').attr('id', null).datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: "1950:2019"});
}

edit();

$('input:text').each(function (index, element) {
  if ($(element).val().trim() && ! document.getElementById('errors').innerHTML.length) {
    save();
    return false;
  }
});

function nodataFlag() {
  document.forms['grids'].elements['nodata'].value = '1';
  document.forms['grids'].submit();
}

function remove(button) {
  var row = $(button).parents('tr');
  if (row.siblings('tr').length) {
    row.remove();
    row.parent().children('tr').each(function(rowindex, row) {
      $(row).find('input[name*="type"]').each(function(inputindex, input) {
        $(input).attr('type[' + rowindex + ']');
      });
    });
  } else {
    row.find('input:text').val('');
    row.find(':checked').prop('checked', false);
    row.find(':selected').prop('selected', false);
  }
}

function add(button) {
  var row = $(button).parents('tr');
  var checked = row.find(':checked');
  $('input[name*="date"]').datepicker('destroy');
  row.after(row.clone());
  row = row.next()
  row.find('input:text').val('');
  row.find('input:radio').prop('checked', false);
  row.parent().children('tr').each(function(rowindex, row) {
    for (var i = 0; i < document.forms['grids'].elements.length; i++) {
      var ending = document.forms['grids'].elements[i].name.match(/\[\d+\]/);
      if ((document.forms['grids'].elements[i].parentNode.parentNode.rowIndex == rowindex + 1 || document.forms['grids'].elements[i].parentNode.parentNode.parentNode.rowIndex == rowindex + 1) && ending != null)
        document.forms['grids'].elements[i].name = document.forms['grids'].elements[i].name.replace(ending, '[' + rowindex + ']');
    }
    $(row).find('a[id^="copyFromAbove"]').each(function(inputindex, input) {
      if (rowindex) {
        $(input).attr('id', 'copyFromAbove[' + rowindex + ']');
        $(input).attr('href', 'javascript:copyAbove(' + rowindex + ');');
        $(input).parent().show();
      }
    });
    $(row).find('input[value^="Director"]').each(function(inputindex, input) {
      if (rowindex) {
        $(input).attr('onclick', 'fillDirector(' + rowindex + ')');
      }
    });
  });
  checked.prop('checked', true);
  $('input[name*="date"]').attr('id', null).datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: "1950:2019"});
}

function fillDirector(rowindex)
{
  if (! document.forms['grids'].elements['position[' + rowindex + ']'].value.length)
    document.forms['grids'].elements['position[' + rowindex + ']'].value = 'Director';
}

function copyAbove(rowindex) {
  document.forms['grids'].elements['address1[' + rowindex + ']'].value = document.forms['grids'].elements['address1[' + (rowindex - 1) + ']'].value;
  document.forms['grids'].elements['address2[' + rowindex + ']'].value = document.forms['grids'].elements['address2[' + (rowindex - 1) + ']'].value;
  document.forms['grids'].elements['address3[' + rowindex + ']'].value = document.forms['grids'].elements['address3[' + (rowindex - 1) + ']'].value;
  document.forms['grids'].elements['city[' + rowindex + ']'].value = document.forms['grids'].elements['city[' + (rowindex - 1) + ']'].value;
  document.forms['grids'].elements['stateprovince[' + rowindex + ']'].value = document.forms['grids'].elements['stateprovince[' + (rowindex - 1) + ']'].value;
  document.forms['grids'].elements['postalcode[' + rowindex + ']'].value = document.forms['grids'].elements['postalcode[' + (rowindex - 1) + ']'].value;
  document.forms['grids'].elements['country[' + rowindex + ']'].value = document.forms['grids'].elements['country[' + (rowindex - 1) + ']'].value;
}
