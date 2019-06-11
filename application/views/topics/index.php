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
	<tr>
        <td>
        <a href="<?php echo base_url(); ?>Topic/update/<?php echo $topic->ID; ?>"><?php echo $topic->Topic; ?></a>
       </td>   
       <td>
    	<a href="<?php echo base_url(); ?>Topic/delete/<?php echo $topic->ID; ?>" class="close">×</a>
    	</td>
    </tr>
	<?php
}
?>

<div id="page-content-wrapper">
    <div class="container">
        <h1 class="title-section"><?php echo $this->lang->line('title_topic'); ?></h1>			
		<?php
			if($error == true) {
				echo "<p class='alert alert-danger'>" . $error . "</p>";
			}
		?>
        <div class="row">
            <div class="col-xs-12">
                <h4><?php echo $this->lang->line('focus_module'); ?></h4>
                <select onchange="changeselectTopic()" class="form-control" id="topic_selected">
                    <?php
                    echo "<option selected disabled hidden></option>";
                    //Récupère chaque module
                    foreach ($modules as $object => $module) {
                        if($module->FK_Parent_Topic == 0)
                        {
                            //Affiche les modules
                            echo "<option value='" . $module->ID . "' ".($module_selected==$module->ID?' selected':'').">" . $module->Topic . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>  
            <div class="col-xs-12 col-sm-4" style="margin-bottom: 10px">
                <a href="<?php echo base_url(); ?>Topic/add/" class="btn btn-success col-xs-12"><?php echo $this->lang->line('btn_add_topic'); ?></a>
            </div>
            <div class="table-responsive col-xs-12">
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
        </div>
    </div>
    <script>
        //window.onload = init();
    </script>