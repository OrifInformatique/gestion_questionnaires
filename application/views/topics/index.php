<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * View of topic's list
 *
 * @author      Orif, section informatique (UlSi, ViDi)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */

function displayTopics($topics){

    $compteur = 0;

    foreach ($topics as $topic)
    {

        if (isset($_GET['param']) && $topic->Topic == $_GET['param'])
        {
            for($index = 0; $index < count($topics); $index++)
            {
                if($topic->ID == $topics[$index]->FK_Parent_Topic)
                {
                    $compteur++;
                    displayTableBody($topics[$index]);
                }
            }
        }
    }
    return $compteur;
}
function displayTableBody($topic){
    ?>
    <tr id="<?php echo $topic->ID; ?>" onclick="getID(<?php echo $topic->ID;?>, 4)"><?php
    echo "<td>";
    echo $topic->Topic;
    echo  "<td>";
    echo "</tr>";
}
?>
<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="">
                    <?php echo $this->lang->line('nav_topic'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('Topic/add');?>"><?php echo $this->lang->line('btn_add'); ?></a>
            </li>
            <li>
                <a id="btn_update"><?php echo $this->lang->line('btn_update'); ?></a>
            </li>
            <li>
                <a id="btn_del"><?php echo $this->lang->line('btn_del'); ?></a>
            </li>
        </ul>
    </div>
</div>
<div id="page-content-wrapper">
    <div class="container">
        <h1 style="padding-top: 12%; padding-bottom: 5%"
            class="text-center"><?php echo $this->lang->line('title_topic'); ?></h1>
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-4" style="height:110px;">
                <h4><?php echo $this->lang->line('focus_module'); ?></h4>
                <select onchange="changeselect()" class="form-control" id="topic_selected">
                    <?php
                    //Récupère chaque module
                    foreach ($topics as $object => $topic) {
                        if($topic->FK_Parent_Topic == 0)
                        {
                            //Affiche les modules
                            echo "<option>" . $topic->Topic . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-6"></div>
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <?php
                    if(isset($_GET['param'])){
                        echo "<h3>" . $_GET['param'] . "</h3>";
                    }
                    ?>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('topic'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $compteur = displayTopics($topics);
                        if($compteur == 0)
                        {
                            foreach ($topics as $topic)
                            {
								if(isset($_GET['param'])){
									if(($topic->FK_Parent_Topic != 0) AND ($topic->Topic == $_GET['param']))
									{
										displayTableBody($topic);
									}
								} else {
									if($topic->FK_Parent_Topic != 0)
									{
										displayTableBody($topic);
									}

								}
                            }
                        }?>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-2"></div>
            </div>
        </div>
    </div>
    <script>
        window.onload = init();
    </script>