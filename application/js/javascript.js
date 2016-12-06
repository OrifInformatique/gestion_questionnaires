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
function deleteItem(id, typeItem) {

    switch(typeItem){
        case 1:
            if (confirm("Voulez-vous supprimer ce questionnaire?")) {
                document.location.href = "../Questionnaire/delete/" + id;
            }
            break;
        case 2:
            if (confirm("Voulez-vous supprimer cette question?")) {
                document.location.href = "../Question/delete/" + id;
            }
            break;
        case 3:
            if (confirm("Voulez-vous supprimer ce module")) {
                document.location.href = "../Module/delete/" + id;
            }
            break;
        case 4:
            if (confirm("Voulez-vous supprimer ce topic?")) {
                document.location.href = "../Topic/delete/" + id;
            }
            break;
    }
}

function init() {
    document.getElementById("topics").selectedIndex = -1;
}

function changeselect() {
    var topic = document.getElementById("topics").value;

    window.location = '?param=' + topic;
}

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
}

function getID(id, typeItem) {

    document.getElementById("btn_del").setAttribute("onclick", "deleteItem(" + id + "," + typeItem + ")");
    document.getElementById("btn_update").setAttribute("onclick", "updateItem(" + id + "," + typeItem + ")");
    document.getElementById(id).setAttribute("style", "border: 3px solid black;");

    if(oldid != '' && oldid != id){
        document.getElementById(oldid).setAttribute("style", "border: none;");
    }

    oldid = id;
}