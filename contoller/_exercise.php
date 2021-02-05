
<!--<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Exercise:</th>
            <th>Sets:</th>
            <th>Reps:</th>
            <th>Seconds:</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $arr_exercises as $ex ){?>
            <tr id="pe_<?php echo $ex->id;?>">
                <td><?php echo $ex->title;?></td>
                <td><?php echo $ex->steps;?></td>
                <td><?php echo $ex->reps;?></td>
                <td><?php echo $ex->time;?></td>
                <td>
                    <a class="btn-delete-exercise" data-id="<?php echo $ex->id;?>" href="javascript:;" data-url="<?php echo site_url('admin/programs/delete_exercises/'.$ex->id);?>">delete</a>
                </td>
            </tr>
        <?php }?>
    </tbody>
</table>-->

<div class="dd" id="nestable" name="nestable">
    <ol class="dd-list">
     <?php foreach( $arr_exercises as $ex ){?>
        <li class="dd-item " data-id="<?php echo $ex->id;?>">
         <div class="row">
            <div class = "col-sm-12">
              <div class="dd-handle dd3-handle"></div>
              <div class=" dd3-content conf"><?php echo $ex->title;?></div>
            </div>
        </div>
         
         <div class="row">
            <div class = "col-sm-3">
              <div class=" dd3-content "><?php echo $ex->steps;?></div>
            </div>
            <div class = "col-sm-3">
              <div class=" dd3-content "><?php echo $ex->reps;?></div>
            </div>

            <div class = "col-sm-3">
              <div class=" dd3-content "><?php echo $ex->time;?></div>
            </div>

            <div class = "col-sm-3">
              <div class=" dd3-content "><a class="btn-delete-exercise" data-id="<?php echo $ex->id;?>" href="javascript:;" data-url="<?php echo site_url('admin/programs/delete_exercises/'.$ex->id);?>">delete</a></div>
            </div>
         </div>
        </li>
     <?php }?>
    </ol>
</div>

