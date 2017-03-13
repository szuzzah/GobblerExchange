 $(function(){

    $(".dropdown-menu li a").click(function(){
  var selText = $(this).text();
  $(this).parents('.input-group-btn').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
});

});