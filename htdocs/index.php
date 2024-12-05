	<?php 
	include 'lib/template.class.php';
	$template = new template();

	session_start(); // Start the session
	
	?>
	
	<!DOCTYPE html>
	<html lang="en" class="no-js">
	<head>
		<?php $template->load_template('template','head'); ?>
	</head>
	<body>
		<!-- header start -->
		<?php $template->load_template('template','header'); ?>
		<!-- header end-->

		<!-- start banner Area -->
		<?php $template->load_template('template','banner-area'); ?>
		<!-- End banner Area -->	

		<!-- Start about Area -->
		<?php $template->load_template('template','about-us-area'); ?>
		<!-- End about Area -->
	
		<!-- Start book Area -->
		<?php $template->load_template('template','book-area'); ?>
		<!-- End book Area -->
		 
		<!-- Start Fact Area -->
		<?php $template->load_template('template','fact-area'); ?>
		<!-- End Fact Area -->

		<!-- Start Counter Area -->
		<?php $template->load_template('template','counter-area'); ?>
		<!-- End Counter Area -->

		
		<!-- Start price Area -->
		<?php $template->load_template('template','pricing-area'); ?>
		<!-- End price Area -->
		
		<!-- Start call-to-action Area -->
		<?php $template->load_template('template','call-to-action-area'); ?>
		<!-- End call-to-action Area -->

		<!-- Start testomial Area -->
		<?php $template->load_template('template','testomial-area'); ?>
		<!-- End testomial Area -->
		

		<!-- start footer Area -->		
		<?php $template->load_template('template','footer'); ?>
		<!-- End footer Area -->	

		<script src="js/vendor/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="js/vendor/bootstrap.min.js"></script>			
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhOdIF3Y9382fqJYt5I_sswSrEw5eihAA"></script>
		<script src="js/easing.min.js"></script>			
		<script src="js/hoverIntent.js"></script>
		<script src="js/superfish.min.js"></script>	
		<script src="js/jquery.ajaxchimp.min.js"></script>
		<script src="js/jquery.magnific-popup.min.js"></script>	
		<script src="js/owl.carousel.min.js"></script>			
		<script src="js/jquery.sticky.js"></script>
		<script src="js/jquery.nice-select.min.js"></script>			
		<script src="js/parallax.min.js"></script>	
		<script src="js/waypoints.min.js"></script>
		<script src="js/jquery.counterup.min.js"></script>			
		<script src="js/mail-script.js"></script>	
		<script src="js/main.js"></script>	
	</body>
	</html>



