$(document).ready(function(){

    //on profile/index


    $("div.below_image").mouseenter(function() {
        $("img.news_image").css("opacity",".5");
        $("div.below_image").css("opacity","1");
    });
    $("div.below_image").mouseout(function() {
        $("img.news_image").css("opacity","1");
        $("div.below_image").css("opacity","0");
    });
    $("img.news_image").mouseenter(function() {
        $("img.news_image").css("opacity",".5");
        $("div.below_image").css("opacity","1");
    });
    $("img.news_image").mouseout(function() {
        $("img.news_image").css("opacity","1");
        $("div.below_image").css("opacity","0");
    });

    $(document).on('mouseup', '#source_tabsx', function(){
        setTimeout(function() {
            $("#source").val($("#source_tabsx li.active").text());
        }, 100);
        query();
    });



    $("#query_button").click(function(){
        performQuery();
    });

    $('input[type=radio][name="QueryForm[category]"]').change(function() {
        performQuery();
    });
    $("#sort_by").change(function () {
    });

    $("#search_box").keydown(function(event){

        if(event.keyCode == 13){
            return false;
        }
    });

    $("a:contains('All')").click(function(){
        $("#category").val('');
        query();
    });


    $("a:contains('Politics')").click(function(){
        $("#category").val('Politics');
        query();
    });


    $("a:contains('Social')").click(function(){
        $("#category").val('Social');
        query();
    });


    $("a:contains('Technology')").click(function(){
        $("#category").val('Technology');
        query();
    });

    $("a:contains('Economy')").click(function(){
        $("#category").val('Economy');
        query();
    });


});

function query(){
    $("#form_up").submit();
}