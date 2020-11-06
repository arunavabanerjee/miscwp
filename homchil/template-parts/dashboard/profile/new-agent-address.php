<?php

/**
 * form for new agent address information
 */
global $userID, $homey_prefix, $homey_local;

?>
<div class="block">
    <div class="block-title">
        <div class="block-left">
            <h2 class="title"><?php echo 'Enter Agent '.esc_html__('Address', 'homey'); ?></h2>
        </div>
    </div>
    <div class="block-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="street_address"><?php echo esc_html__('Street Address', 'homey'); ?></label>
                    <input type="text" id="street_address" name="street_address" class="form-control" placeholder="<?php echo esc_attr__('Enter Full Address in the form of: Address, City, State Postcode', 'homey'); ?>">
                </div>
            </div>
            <!--<div class="col-sm-3">
                <div class="form-group">
                    <label for="apt_suit"> <?php echo esc_html__('Apt, Suite', 'homey'); ?> </label>
                    <input type="text" id="apt_suit" class="form-control" placeholder=" <?php echo esc_attr__('Ex. #123', 'homey'); ?> ">
                </div>
            </div>-->
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="city"><?php echo esc_html__('City', 'homey'); ?></label>
		    <select class="form-control select" id="city" name="city" style="padding:0 5px;">
	     		<option value="">Select City</option><?php echo homey_get_all_cities(); ?>
	   	    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="state"><?php echo esc_html__('State', 'homey'); ?></label>
		    <select class="form-control select" id="state" name="state" style="padding:0 5px;">
	     		<option value="">Select States</option><?php echo homey_get_all_states(); ?>
	   	    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="zipcode"><?php echo esc_html__('Postal Code', 'homey'); ?></label>
                    <input type="text" id="postalcode" name="postalcode" class="form-control" placeholder="<?php echo esc_attr__('Enter postal or zip code', 'homey'); ?>">
                </div>
            </div>
            <!--<div class="col-sm-6">
                <div class="form-group">
                    <label for="neighborhood"><?php echo esc_html__('Neighborhood', 'homey'); ?></label>
                    <input type="text" id="neighborhood" class="form-control" value="<?php echo esc_attr($neighborhood); ?>" placeholder="<?php echo esc_attr__('Neighborhood', 'homey'); ?>">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="counter"><?php echo esc_html__('Country', 'homey'); ?></label>
                    <input type="text" id="country" class="form-control" value="<?php echo esc_attr($country); ?>" placeholder="<?php echo esc_attr__('country', 'homey'); ?>">
                </div>
            </div>-->
            <div class="col-sm-12 text-right">
		<div class="homey_add_new_agent_messages_bottom"></div>
                <button type="submit" class="homey_new_agent_save btn btn-success btn-xs-full-width"><?php echo esc_attr($homey_local['save_btn']); ?></button>
            </div>
        </div>
    </div>
</div><!-- block -->
</form>
</div>

<!--for dashboard -->
</div></div></div>
</div></div></div>
