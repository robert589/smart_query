$(document).ready(function(){

    //on profile/index
    
    // $("div.below_image").mouseenter(function() {
    //     $("img.news_image").css("opacity",".5");
    //     $("div.below_image").css("opacity","1");
    // });
    // $("div.below_image").mouseout(function() {
    //     $("img.news_image").css("opacity","1");
    //     $("div.below_image").css("opacity","0");
    // });
    // $("img.news_image").mouseenter(function() {
    //     $("img.news_image").css("opacity",".5");
    //     $("div.below_image").css("opacity","1");
    // });
    // $("img.news_image").mouseout(function() {
    //     $("img.news_image").css("opacity","1");
    //     $("div.below_image").css("opacity","0");
    // });

    $(document).on('mouseup', '#source_tabsx', function(){
        setTimeout(function() {
            $("#source").val($("#source_tabsx li.active").text());
        }, 100);
        query();
    });

    $("#query_button").click(function(){
        $("#query").val($("#search_box").val());

        query();
    });

    $("#search_box").keydown(function(event){
        if(event.keyCode == 13){
            $("#query").val($("#search_box").val());
            query();
            return false;
        }
    });

    $("input:radio[name=category_sidenav]").change(function(){
        var category =  $(this).closest('label.active input:radio').val();

        $("#category").val(category);
        query();
    });


    $("input:radio[name=sort_by_sidenav]").change(function(){
        var sort_by =  $(this).closest('label.active input:radio').val();

        $("#sort_by").val(sort_by);
        query();
    });


});

function query(){
    $("#form_up").submit();
}