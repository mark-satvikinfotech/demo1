<?php
set_time_limit(0);
date_default_timezone_set('Europe/London');
error_reporting(E_ERROR | E_PARSE);
?>
<div class="wrap">
<h2>DoceboLabs</h2>
	<div class="bf_docebolabs_container">
		<h3>Docebo API Settings Help</h3>
		<p><b>Instructions for Docebo API Connections:</b></p>

		<p>1 - Sign into your Docebo account</p>

		<p>2 - Click the Settings cog</p>


		<p><b>If first time and no apis setup previously:</b></p>

		<p>3 - Add New Apps</p>

		<p>4 - Third party intergrations</p>

		<p>5 - Choose API and SSO</p>

		<p>6 - In next page under My Apps, click the settings cog for API and SSO</p>


		<p><b>If API and SSO already active on account:</b></p>

		<p>7 - API AND SSO -> Manage</p>


		<p><b>Once on API and SSO Setings page:</b></p>

		<p>8 - Enter API Credentials</p>

		<p>9 - Add OAuth 2 App</p>

		<p>10 - Fill fields as:</p>
		<ul style="margin-left: 1em;">
			<li> - App name to anything i.e, Docebolabs</li>
			<li> - App Description anything i.e, Docebolabs wordpress plugin</li>
			<li> - Client ID anything i.e, business ltd</li>
			<li> - Client Secret pre filled out</li>
			<li> - Redirect uri should be the domain the wordpress plugin is installed i.e, https://www.domain.com</li>
			<li> - Now click show advanced settings</li>
			<li> - Ensure the following are ticked:</li>
			<li> - Authorization code + Implicit Grant</li>
			<li> - Resource Owner Password Credentials</li>
			<li> - Client Credentials</li>
		</ul>

		<p>11 - Click Confirm</p>

		<p>12 - You will now see the OAuth Application listed, click the tick in a circle to make it green.</p>

	</div>
</div>