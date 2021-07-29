(function($){
  $(function(){
      
    $(".dropdown-trigger").dropdown();
    $('select').formSelect();
    $('input#input_text, textarea#textarea2').characterCounter();
      
  }); // end of document ready
})(jQuery); // end of jQuery name space
