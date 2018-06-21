/**
 * Javascript file for dynamic pages
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
var oldid = '';

/**
 * Confirm the delete
 * @param id = id of the item
 * @param typeItem :
 * 1 = Questionnaire
 * 2 = Question
 * 3 = Module
 * 4 = Topic
 */
 /*
function deleteItem(id, typeItem) {

    switch(typeItem){
        case 1:
            if (confirm("Voulez-vous supprimer ce questionnaire?")) {
                document.location.href = "./Questionnaire/delete/" + id;
            }
            break;
        case 2:
            if (confirm("Voulez-vous supprimer cette question?")) {
                document.location.href = "./Question/delete/" + id;
            }
            break;
        case 3:
            if (confirm("Voulez-vous supprimer ce module")) {
                document.location.href = "./Module/delete/" + id;
            }
            break;
        case 4:
            if (confirm("Voulez-vous supprimer ce topic?")) {
                document.location.href = "./Topic/delete/" + id;
            }
            break;
    }
}*/

function changeselect() {
    var module = document.getElementById("module_selected").value;
    var topic = document.getElementById("topic_selected").value;
    var type = document.getElementById("question_type_selected").value;

    window.location = '?module=' + module + '&topic=' + topic + '&type=' + type;
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
function changePage(page){
    window.location =  updateURLParameter(window.location.toString(), "page", page);
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

/*
function updateItem(id, typeItem){

    switch (typeItem){
        case 1:
            document.location.href = "./Questionnaire/update/" + id;
            break;
        case 2:
            document.location.href = "./Question/update/" + id;
            break;
        case 3:
            document.location.href = "./Module/update/" + id;
            break;
        case 4:
            document.location.href = "./Topic/update/" + id;
            break;
    }
}*/

/*
function getID(id, typeItem) {

    document.getElementById("btn_del").setAttribute("onclick", "deleteItem(" + id + "," + typeItem + ")");
    document.getElementById("btn_update").setAttribute("onclick", "updateItem(" + id + "," + typeItem + ")");
    document.getElementById(id).setAttribute("style", "border: 3px solid black;");
   
    if(oldid != '' && oldid != id){
        document.getElementById(oldid).setAttribute("style", "border: none;");
    }

    oldid = id;
}*/
