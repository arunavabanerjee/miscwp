<?php
/**
 * Template Name: My Accout Template
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

<?php
$pageid = get_the_ID();
while ( have_posts() ) : the_post();
?>

<div class="container inner-cont">
<div class="row">    
 <div class="dashboard-section">
   <div id="exTab1" class="container">

	<!-- navigation -->
        <div class="col-sm-4 col-xs-12">
        <ul class="nav nav-pills">
          <li class="active">
            <span class="dash-icon"><i class="fa fa-tachometer" aria-hidden="true"></i></span>
            <a href="#1a" data-toggle="tab">Dashboard</a>
          </li>
          <li>
            <span class="dash-icon"><i class="fa fa-sort" aria-hidden="true"></i></span>
            <a href="#2a" data-toggle="tab">Recent Orders</a>
          </li>
          <li>
            <span class="dash-icon"><i class="fa fa-asterisk" aria-hidden="true"></i></span>
            <a href="#3a" data-toggle="tab">My details</a>
          </li>
          <li>
            <span class="dash-icon"><i class="fa fa-truck" aria-hidden="true"></i></span>
            <a href="#4a" data-toggle="tab">Shipping Address</a>
          </li>                                   
          <li>
            <span class="dash-icon"><i class="fa fa-trophy" aria-hidden="true"></i></span>
            <a href="#5a" data-toggle="tab">Scheduled Games</a>
          </li>
          <li>
            <span class="dash-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
            <a href="#6a" data-toggle="tab">Friends </a>
          </li>
        </ul>
        </div>
        
	<!-- tabs -->
	<div class="col-sm-8 col-xs-12">
          <div class="tab-content clearfix">




            <div class="tab-pane active" id="1a">
		<?php //wc_get_template( 'myaccount/my-account.php' ); ?>
		<?php the_content(); ?>
            </div>

            <div class="tab-pane" id="2a">
               <?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
            </div>

            <div class="tab-pane" id="3a">
              <div class="row">
               <?php wc_get_template( 'myaccount/form-edit-account.php' ); ?>
              </div>
            </div>

            <div class="tab-pane" id="4a">
              <div class="row">
               <div class="col-sm-6 col-xs-12">
                 <?php wc_get_template( 'myaccount/my-address.php' ); ?>
               </div>
              </div>
            </div>

            <div class="tab-pane" id="5a">
               <?php get_template_part('template-parts/content', 'games'); ?>
            </div>

            <div class="tab-pane" id="6a">                                      
		<?php get_template_part('template-parts/content', 'friends'); ?>
            </div> 
	  </div>
        </div> 

   </div>
 </div>            
</div>
</div>

<?php endwhile; ?>

<?php
get_footer();
