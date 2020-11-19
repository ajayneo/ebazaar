<?php
	class Neo_Customapi_StaticController extends Mage_Core_Controller_Front_Action
	{
		    /**
				 * @desc : contact us
				 * @author : bhargav rupapara
				 */

		public function contactUsHtmlAction()
		{
			$html = '<div style="min-width: 320px;">
		<div style="margin: 0px auto; border: 1px solid #dedede; width: 95%;">

			<ul style=" display: block; list-style: outside none none; margin: 0; padding:10px;">
				<li style="clear: both; display: block; margin: 0 0 15px; width: 100%;overflow: hidden;">
					<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
						<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-call.png" alt="call">
					</div>
					<div style="width: 80%; float: left;">
						<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Contact Number</label>
						<span style="font-size: 15px; line-height: 18px; color:#313131">1800-266-4000<br>Monday - Friday, 9:30am - 6:30pm IST.</span>
					</div>
				</li>

				<li style="clear: both; display: block; margin: 0 0 15px; width: 100%; overflow: hidden;">
					<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
						<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-email.png" alt="Email">
					</div>
					<div style="width: 80%; float: left;">
						<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Email</label>
						<span style="font-size: 15px; line-height: 18px; color:#313131"><a href="mailto:support@electronicsbazaar.com">support@electronicsbazaar.com</a></span>
					</div>
				</li>



				<li style="clear: both; display: block; margin: 0px; width: 100%; overflow: hidden;">
					<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
						<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-pin.png" alt="location">
					</div>
					<div style="width: 80%; float: left;">
						<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Address</label>
						<span style="font-size: 15px; line-height: 18px; color:#313131">ElectronicsBazaar.com <br> 415, Hubtown Solaris,N.S.Phadke Marg,Near East West Flyover Bridge,Andheri East, Mumbai,Maharastra - 400069</span>.
					</div>
				</li>
			</ul>
		</div>
	</div>';
			echo $html;
		}

}
?>
