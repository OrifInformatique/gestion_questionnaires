/**
 * Javascript file for dynamic pages
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

function changeselect() {
    var module = document.getElementById("module_selected").value;
    var topic = document.getElementById("topic_selected").value;
    var type = document.getElementById("question_type_selected").value;
    var search = document.getElementById("search").value;

    window.location = '?module=' + module + '&topic=' + topic + '&type=' + type + '&search=' + search;
}


function changeselectTopic() {
    var topic_selected = document.getElementById("topic_selected").value;
    window.location = '?topic_selected=' + topic_selected;
}
function sortClick(actual_sort, sort_click){
    var sort = "";
    if(actual_sort == sort_click + '_asc')
    {
        sort = sort_click + '_desc';
    }
    else
    {
        sort = sort_click + '_asc';
    }
    window.location =  updateURLParameter(window.location.toString(), "sort", sort);

}
function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

function invertInputs(button, total, direction){

    let index = parseInt(button.getAttribute('data-button-id'));

    let destination = index+direction;
    console.log(index, destination);

    if(destination >= 0 && destination < total){

        let indexDOM = $("[data-row-id="+index+"]");
        let destinationDOM = $("[data-row-id="+destination+"]");

        if(direction > 0){
            indexDOM.insertAfter(destinationDOM);
        } else if(direction < 0){
            indexDOM.insertBefore(destinationDOM);
        }
        indexDOM.attr('data-row-id', destination);
        destinationDOM.attr('data-row-id', index);

        let button1 = $("[data-button-id="+index+"]");
        let button2 = $("[data-button-id="+destination+"]");
        button1.attr('data-button-id', destination);
        button2.attr('data-button-id', index);
    }
}