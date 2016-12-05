<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Questionnaire List View
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
$_SESSION
?>
<div id="wrapper">

    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="#">
                    <?php echo $this->lang->line('nav_questionnaire');?>
                </a>
            </li>
            <li>
                <a href="#"><?php echo $this->lang->line('btn_add');?></a>
            </li>
            <li>
                <a id="btn_update"><?php echo $this->lang->line('btn_update');?></a>
            </li>
            <li>
                <a id="btn_del"><?php echo $this->lang->line('btn_del');?></a>
            </li>
        </ul>
    </div>
</div>
<div id="page-content-wrapper">
    <div class="container">
        <h1 style="padding-top: 12%; padding-bottom: 5%" class="text-center"><?php echo $this->lang->line('title_questionnaire');?></h1>
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <table class="table table-striped" id="table">
                    <thead>
                    <tr>
                        <th><?php echo $this->lang->line('questionnaire');?></th>
                        <th><?php echo $this->lang->line('pdf');?></th>
                        <th><?php echo $this->lang->line('corrige');?></th>
                    </tr>
                    </thead>
                    <?php
                    foreach ($questionnaires as $questionnaire){
                        ?>
                        <tbody>
                        <tr onclick="getID(<?php echo $questionnaire->ID; ?>);"
                            id="<?php echo $questionnaire->ID; ?>">
                                <td><?php echo $questionnaire->Questionnaire_Name; ?></td>
                                <td><a href="" class="btn btn-success"><?php echo $this->lang->line('redirect_pdf');?></a></td>
                                <td><a href="" class="btn btn-success"><?php echo $this->lang->line('redirect_corrige');?></a></td>
                        </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>
                <a href="" class="btn btn-info"><?php echo $this->lang->line('btn_add');?></a>
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
</div>
<script>


    function deleteQuestionnaire(id){
        if(confirm("Voulez-vous supprimer ce questionnaire ?")){
            document.location.href = "../Questionnaire/delete/" + id;
        }else{

        }
    }

    function getID(id) {
        alert('jjlé');
        /**
         *
        <?php
            if(isset($_SESSION['currentSelected']))
            {
        ?>
                document.getElementById(<?php echo $_SESSION['currentSelected'];?>).setAttribute("style", "background-color: white;");
        <?php
            }
        ?>
        document.getElementById("btn_del").setAttribute("onclick", "deleteQuestionnaire(" + id + ")");
        document.getElementById(id).setAttribute("style", "background-color: #dbdbdb;");
        <?php $_SESSION['currentSelected'] ?> = id;
         */

    }

</script>
