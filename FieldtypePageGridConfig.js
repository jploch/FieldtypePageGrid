$(document).ready(function () {
  console.log('FieldtypePageGird js loaded');

  //fix inputfield customStyles height
  var customStyles = $('#Inputfield_customStyles');
  // console.log(url);

  //set height
  customStyles.css('min-height', customStyles.prop('scrollHeight') + 'px');
  console.log(customStyles);
  console.log(customStyles.scrollHeight + 'px');

  //make header open more easy
  $('.Inputfield').addClass('InputfieldStateWasCollapsed');

  //save collapsed states
  $('.InputfieldHeader').click(function (e) {

    setTimeout(() => {
      console.log('click');
      var collapsed = $('.InputfieldStateCollapsed');
      var value = '';
      collapsed.each(function () {
        var name = $(this).attr('id');
        var name = name.replace("Inputfield_", "");
        var name = name.replace("wrap_", "");
        console.log(name);
        value += name + ',';
      });

      $('#Inputfield_collapsedState').val(value);
    }, 500)

  });

  //font uploader
  if ($('.Inputfield_uploadFonts').length == 0) {
    return;
  }

  //add attribute to form to make it work with fileuploader inside wrapper
  $('form').attr('enctype', 'multipart/form-data');

  //styling
  $('.Inputfield_uploadFonts .InputfieldHeader').hide();
  $('<style>.AdminThemeCanvas .Inputfield_customFontList tr{border:2px solid #fff;}</sytle>').appendTo($('body'));

  //hide false error
  var errors = $('.NoticeError');
  errors.each(function () {
    var error = $(this);
    var errorText = error.text();
    var hasString = "Unable to move uploaded file";
    if (errorText.includes(hasString)) {
      error.hide();
    }
  });

  $('#notices').show();

  //make sure the delete field is empty
  $('#Inputfield_deleteFonts').val('');

  //mark deleted
  $('.Inputfield_customFontList .delete-file').click(function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var deleteField = $('#Inputfield_deleteFonts');
    row.toggleClass('InputfieldIsError pg-font-delete');

    var val = $('.pg-font-delete label').text();

    deleteField.val(val);

  });

});
