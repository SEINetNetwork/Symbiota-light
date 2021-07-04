<script>
<!--
if (top.frames.length!=0)
  top.location=self.document.location;
// -->
</script>
<header >
	<div>
		<div class="container" style="background-color:#031a12; color:#FFFFFF">
			<img style="margin-top:5px;margin-bottom:5px;width:300px;" src="<?php echo $CLIENT_ROOT; ?>/images/layout/EcoFlorasNA_Banner_small.png"/>
			<div id="quicksearchdiv" style="float:right">
				<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
				<form name="quicksearch" id="quicksearch" action="/portal/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
					<div id="quicksearchtext">Search Taxon</div>
					<input id="taxa" type="text" name="taxon" style="width: 225px">
					<button name="formsubmit" id="quicksearchbutton" type="submit" value="Search Terms" style="margin-top: 2px">Search taxon</button>
				</form>
			</div>
		</div>
	</div>
	<div id="top_navbar">
		<div id="right_navbarlinks">
					<?php
					if($USER_DISPLAY_NAME){
						?>
						<span style="">
							Welcome <?php echo $USER_DISPLAY_NAME; ?>!
						</span>
						<span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php">My Profile</a>
						</span>
						<span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout">Logout</a>
						</span>
						<?php
					}
					else{
						?>
						<span style="">
							<a href="<?php echo $CLIENT_ROOT.'/profile/index.php?refurl='.$_SERVER['SCRIPT_NAME'].'?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
								Log In
							</a>
						</span>
						<span style="margin-left:5px;">
							<a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php">
								New Account
							</a>
						</span>
						<?php
					}
					?>
					<span style="margin-left:5px;margin-right:5px;">
						<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'>Sitemap</a>
					</span>

				</div>
				<ul id="hor_dropdown">
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/index.php" >Home</a>
					</li>
					<li>
						<a href="#" >Search</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" >Search Collections</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php" >Search Images</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank">Map Search</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" >Chicago Botanic Garden</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/misc/CBG_about.php" >Project Information</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=140" >Checklists</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist&lat=41.89&long=-87.67&zoom=9" >Create a Checklist</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key&lat=41.89&long=-87.67&zoom=9" >Dynamic Key</a>
							</li>
						</ul>						
					</li>
					<li> 
						<a href="#" >Denver Botanic Gardens</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/misc/DenBG_about.php" >Project Information</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=141" >Checklists</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist&lat=39.72&long=-104.96&zoom=9" >Create a Checklist</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key&lat=39.72&long=-104.96&zoom=9" >Dynamic Key</a>
							</li>
						</ul>
					</li>	
					<li>
						<a href="#" >Desert Botanical Garden</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/misc/DesBG_about.php" >Project Information</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=139" >Checklists</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist&lat=33.51&long=-112.08&zoom=9" >Create a Checklist</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key&lat=33.51&long=-112.08&zoom=9" >Dynamic Key</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" >NY Botanical Garden</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/misc/NYBG_about.php" >Project Information</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=115" >Checklists</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist&lat=40.75&long=-73.99&zoom=10" >Create a Checklist</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key&lat=40.75&long=-73.99&zoom=10" >Dynamic Key</a>
							</li>
						</ul>
					<li>
						<a href="#" >Marie Selby Botanical Gardens</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/misc/SBG_about.php" >Project Information</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=138" >Checklists</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist&lat=27.32&long=-82.34&zoom=9" >Create a Checklist</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=key&lat=27.32&long=-82.34&zoom=9" >Dynamic Key</a>
							</li>
						</ul>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<td id='middlecenter'  colspan="3">