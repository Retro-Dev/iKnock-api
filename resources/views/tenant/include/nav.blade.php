<div class="row nomargin">
	<div class="top_nav">
		<div class="nav_menu">
			<nav>
				<div class="nav toggle">
					<a href="{{ URL::to('/tenant/dashboard') }}" style="padding:0px;"> <img src="{{ asset('image/nav-logo.png') }}"class="img-responsive"></a>
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li class="view_user">
						
						<ul class="dropdown-menu dropdown-usermenu pull-right" >
							 
							 <li>
								 <a href="{{ URL::to('/tenant/agent/edit_profile') }}">Settings</a>
							 </li>
							 <li>
								 <a href="{{ URL::to('/tenant/logout') }}">Log Out</a>
							 </li>
						</ul>
					</li>
					<li><a href="{{ URL::to('/tenant/lead/add_lead') }}" style="background:#0a326e; color:white;padding:0px 35px;"><i class="fas fa-plus-circle" style="padding-right:10px;"></i>Add Lead</a></li>
				</ul>
			</nav>
		</div>
	</div>
</div>


<script type="text/javascript">
$(document).ready(function(){
    
    var columns = ['nav_user_name','user_image'];    
    getEditRecord('POST',base_url + "/tenant/user/profile",{},{},columns); // UPDATE FUNCTION

})
</script>