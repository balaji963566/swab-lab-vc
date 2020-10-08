<div class="collapse navbar-collapse" id="navbar-collapse">
	<ul class="nav navbar-nav" style="float: none;margin: 0 auto;width: fit-content;">
	    <li>
	        <a href="{{ route("admin.home") }}">
	            <i class="fas fa-fw fa-tachometer-alt"></i>
	            <span class="menu-item">{{ trans('global.dashboard') }}</span>
	        </a>
	    </li>
	    @can('facility_management_access')
		    <li>
		        <a href="{{ route("admin.facilities.index") }}">
		            <i class="fas fa-fw fa-hospital-o"></i>
		            <span class="menu-item">Facility / Hospital</span>
		        </a>
		    </li>
	    @endcan
	    @can('inwards_management_access')
		    <li class="dropdown">
		        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		            <i class="fa-fw fas fa-users"></i>
		            <span class="menu-item">Inwards</span>
		            <span class="caret"></span>
		        </a>
		        <ul class="dropdown-menu" role="menu">
		        	@can('inwards_all_samples')
			        	<li class="{{ request()->is('admin/inwards') ? 'active' : '' }}">
			                <a href="{{ route("admin.inwards.index") }}">
			                    <span>All Samples</span>
			                </a>
			            </li>
		            @endcan
		            @can('inwards_add_samples')
			            <li class="{{ request()->is('admin/inwards/create') ? 'active' : '' }}">
			                <a href="{{ route("admin.inwards.create") }}">
			                    <span>Add Samples</span>
			                </a>
			            </li>
		            @endcan
					@can('inwards_bulk_samples')
			            <li class="{{ request()->is('admin/inwards/bulk-samples') ? 'active' : '' }}">
			                <a href="{{ route("admin.inwards.bulkSample") }}">
			                    <span>Bulk Samples Upload</span>
			                </a>
			            </li>
		            @endcan
		            @can('inwards_pick_samples')
			            <li class="{{ request()->is('admin/inwards/pick-samples') || request()->is('admin/inwards/select-samples') ? 'active' : '' }}">
			                <a href="{{ route("admin.inwards.pick") }}">
			                    <span>Pick Samples</span>
			                </a>
			            </li>
		            @endcan
		            @can('inwards_sample_status')
			            <li class="{{ request()->is('admin/inwards/status-samples') || request()->is('admin/inwards/mark-status') ? 'active' : '' }}">
			                <a href="{{ route("admin.inwards.status") }}">
			                    <span>Sample Status</span>
			                </a>
			            </li>
		            @endcan
		        </ul>
		    </li>
	    @endcan
	    @can('reports_management_access')
		    <li class="dropdown">
		        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		            <i class="fa-fw fas fa-file-text"></i>
		            <span class="menu-item">Reports</span>
		            <span class="caret"></span>
		        </a>
		        <ul class="dropdown-menu" role="menu">
		        	@can('reports_all')
			        	<li class="{{ request()->is('admin/reports') ? 'active' : '' }}">
			                <a href="{{ route("admin.reports.index") }}">
			                    <span>All Reports</span>
			                </a>
			            </li>
		            @endcan
		            @can('reports_generation_view')
			            <li class="{{ request()->is('admin/reports/generate') ? 'active' : '' }}">
			                <a href="{{ route("admin.reports.generate") }}">
			                    <span>Generate Report</span>
			                </a>
			            </li>
		            @endcan
		            @can('state_reports_view')
			            <li class="{{ request()->is('admin/reports/state-reports') ? 'active' : '' }}">
			                <a href="{{ route("admin.reports.stateReports") }}">
			                    <span>State Reports</span>
			                </a>
			            </li>
		            @endcan
					@can('dline_reports_view')
			            <li class="{{ request()->is('admin/reports/dline-reports') ? 'active' : '' }}">
			                <a href="{{ route("admin.reports.dlineReports") }}">
			                    <span>D-line Reports</span>
			                </a>
			            </li>
		            @endcan
		        </ul>
		    </li>
	    @endcan
	    @can('user_management_access')
	        <li class="dropdown">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                <i class="fa-fw fas fa-user"></i>
	                <span class="menu-item">{{ trans('cruds.userManagement.title') }}</span>
	                <span class="caret"></span>
	            </a>
	            <ul class="dropdown-menu" role="menu">
	                @can('permission_access')
	                    <li class="{{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
	                        <a href="{{ route("admin.permissions.index") }}">
	                            <i class="fa-fw fas fa-unlock-alt">

	                            </i>
	                            <span>{{ trans('cruds.permission.title') }}</span>
	                        </a>
	                    </li>
	                @endcan
	                @can('role_access')
	                    <li class="{{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
	                        <a href="{{ route("admin.roles.index") }}">
	                            <i class="fa-fw fas fa-briefcase">

	                            </i>
	                            <span>{{ trans('cruds.role.title') }}</span>
	                        </a>
	                    </li>
	                @endcan
	                @can('user_access')
	                    <li class="{{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
	                        <a href="{{ route("admin.users.index") }}">
	                            <i class="fa-fw fas fa-user">

	                            </i>
	                            <span>{{ trans('cruds.user.title') }}</span>
	                        </a>
	                    </li>
	                @endcan
	            </ul>
	        </li>
	    @endcan
	    @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
	        @can('profile_password_edit')
	            <li class="{{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}">
	                <a href="{{ route('profile.password.edit') }}">
	                    <i class="fa-fw fas fa-key"></i>
	                    <span class="menu-item">{{ trans('global.change_password') }}</span>
	                </a>
	            </li>
	        @endcan
	    @endif
	    <li>
	        <a href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
	            <i class="fas fa-fw fa-sign-out-alt"></i>
	            <span class="menu-item">{{ trans('global.logout') }}</span>
	        </a>
	    </li>
	</ul>
</div>