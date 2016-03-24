$(document).ready(function(){

    var category = $("#category").val();

    if(category == ''){
        $("a:contains('All')").closest('li').addClass('active');
    }
    else {
        $("a:contains("+ category + ")").closest('li').addClass('active');
    }

    if($("#has_data").val() == false ){
        performQuery();
    }

    $("#query_button").click(function(){
        performQuery();
    });

    $('input[type=radio][name="QueryForm[category]"]').change(function() {
        performQuery();
    });
    $("#sort_by").change(function () {
        performQuery();
    })

    $("#search_box").keydown(function(event){

        if(event.keyCode == 13){
            performQuery();
            return false;
        }
    })

    $("a:contains('All')").click(function(){
        $("#category").val('');
        performQuery();
    });


    $("a:contains('Politics')").click(function(){
        $("#category").val('Politics');
        performQuery();
    });


    $("a:contains('Social')").click(function(){
        $("#category").val('Social');
        performQuery();
    });


    $("a:contains('Technology')").click(function(){
        $("#category").val('Technology');
        performQuery();
    });

    $("a:contains('Economy')").click(function(){
        $("#category").val('Economy');
        performQuery();
    });


});

function performQuery(){
    var query = $("#search_box").val();
    if(query == ''){
        query  = '*';
    }
    var url = 'http://solr.kenrick95.xyz/solr/cz4034/select?q=message%3A' +  query + '&wt=json&indent=true';
    $("#query").val(query);
    if($("#queryform-category").find(".active").length == 1){
        var category = $("#queryform-category").find(".active").html();
        var n = category.split(" ");
        category = n[n.length - 1];
        if(category != 'All'){
            url += '&fq=source:' + category;
        }
    }

    if($("#category").val() != ""){
        url += '&fq=category%3A' + $("#category").val();
    }

    if($("#sort_by").val() == "Latest"){
        url += "&sort=created_time+desc";
    }
    else if($("#sort_by").val() == "Popularity"){
        url += "&sort=like_count+desc";
    }

    $.ajax({
        type     :'POST',
        dataType: 'jsonp',
        jsonp : 'json.wrf',
        cache    : false,
        url  : 'http://solr.kenrick95.xyz/solr/cz4034/spell?q=message%3A'+ query + '&rows=0&wt=json&indent=true',
        success  : function(response) {
            if(response.spellcheck.correctlySpelled == false){
                console.log("it is here");
                $("#spell_checker").val(response.spellcheck.suggestions[1].suggestion[0].word);
            }
            else{
                $("#spell_checker").val(null);
            }

        }
    });

    $.ajax({
        type     :'POST',
        dataType: 'jsonp',
        jsonp : 'json.wrf',
        cache    : false,
        url  : url,
        success  : function(response) {
            console.log(typeof(response.response.docs));
            var json_data = JSON.stringify(response.response.docs);

            $("#hi_data").val(json_data);
            $("#query_form").submit();

        }
    });

}