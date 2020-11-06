<?php

/**
 * form for new agent address information
 */
global $userID, $homey_prefix, $homey_local;

//renter query
$r_args['role__in'] = array('homey_renter');
$renters = new WP_User_Query( $r_args ); 
//agent query
$a_args['role__in'] = array('homey_agents');
$agents = new WP_User_Query( $a_args ); 

?>
<div class="block">
    <div class="block-title">
        <div class="block-left">
            <h2 class="title"><?php echo 'Enter Lead '.esc_html__('Client & Agent Information', 'homey'); ?></h2>
        </div>
    </div>
    <div class="block-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="select-user" style="margin-top:10px;"><?php echo esc_html__('Select User For Lead Generation :'); ?></label>
                </div>
            </div>

            <div class="col-sm-8">
		<?php if ( ! empty( $renters->get_results() ) ) { $html = ''; 
		   foreach ( $renters->get_results() as $user ) {  
		     $html .= '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
		   } } ?>
                <div class="form-group">
		    <select class="form-control select" id="renter-id" name="renter-id" style="width:75%;height:42px;padding:0;">
	     	      <option value="">Select A Customer</option><?php echo $html; ?>
		    </select>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label for="select-user" style="margin-top:10px;"><?php echo esc_html__('Assign An Agent For Lead :'); ?></label>
                </div>
            </div>

            <div class="col-sm-8">
		<?php if ( ! empty( $agents->get_results() ) ) { $html = ''; 
		   foreach ( $agents->get_results() as $user ) {  
		     $html .= '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
		   } } ?>
                <div class="form-group">
		    <select class="form-control select" id="agent-id" name="agent-id" style="width:75%;height:42px;padding:0;">
	     	      <option value="">Select An Agent</option><?php echo $html; ?>
		    </select>
                </div>
            </div>

            <div class="col-sm-12 text-right">
		<div class="homey_add_new_lead_messages_bottom"></div>
                <button type="submit" style="float:left;" class="homey_new_lead_save btn btn-success btn-xs-full-width" name="homey_new_lead_save"><?php echo esc_attr($homey_local['save_btn']); ?></button>
            </div>
        </div>
    </div>
</div><!-- block -->
</form>
</div>

<!--for dashboard -->
</div></div></div>
</div></div></div>
