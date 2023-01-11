<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('railway') }}'><i class='nav-icon la la-sitemap'></i> Railways</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('organization') }}'><i class='nav-icon la la-sitemap'></i> Organizations</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('message') }}'><i class='nav-icon la la-envelope'></i> Messages</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('result') }}'><i class='nav-icon la la-stream'></i> Results</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('member') }}'><i class='nav-icon la la-question'></i> Members</a></li>