<!--sidebar-->
<div class="col-md-3 left_col">
    <div class="left_col scroll-view">

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <ul class="nav side-menu">
                    <li class="" style="margin-top:20px;">
                        <a href="{{ URL::to('tenant/dashboard') }}">
                            <i class="fas fa-chart-line" rel="tooltip" title="Dashboard" ></i>
                            Dashboard
                        </a>

                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/lead/lead_type') }}">
                            <i class="fas fa-file-alt" rel="tooltip" title="Lead Type" ></i>
                            Lead Type
                        </a>

                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/template') }}">
                            <i class="fas fa-book" rel="tooltip" title="Lead Upload Template"></i>
                            Lead Upload Template
                        </a>

                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/agent') }}">
                            <i class="fas fa-address-card" rel="tooltip" title="User Management" ></i>
                            User Management
                        </a>

                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/printer_email') }}">
                            <i class="fas fa-envelope-open-text" rel="tooltip" title="Printer Email" ></i>
                            Printer Email
                        </a>

                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/lead') }}">
                            <i class="fas fa-users" rel="tooltip" title="Lead Management"></i>
                            Lead Management
                        </a>
                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/lead/lead_status') }}">
                            <i class="fas fa-info-circle" rel="tooltip" title="Lead Status"></i>
                            Lead Status
                        </a>
                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/field') }}">
                            <i class="fas fa-text-width" rel="tooltip" title="Field Management"></i>
                            Field Management
                        </a>
                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/commission') }}">
                            <i class="fas fa-dollar-sign" rel="tooltip" title="Commission Management"></i>
                            Commission Management
                        </a>

                    </li>
                    <li class="">
                        <a href="{{ URL::to('tenant/commission_event') }}">
                            <i class="fas fa-wallet" rel="tooltip" title="Commission Event"></i>
                            Commission Event
                        </a>

                    </li>

                    <li class="">
                        <a href="{{ URL::to('tenant/training') }}">
                            <i class="fas fa-tasks" rel="tooltip" title="Training Management"></i>
                            Training Management
                        </a>

                    </li>
                    <li class="">
                        <a href="{{ URL::to('tenant/scheduling') }}">
                            <i class="far fa-calendar-alt" rel="tooltip" title="Scheduling"></i>
                            Scheduling
                        </a>

                    </li>
                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
        {{--<div class="sidebar-footer hidden-small">--}}
            {{--<a data-toggle="tooltip" data-placement="top" title="Settings">--}}
                {{--<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>--}}
            {{--</a>--}}
            {{--<a data-toggle="tooltip" data-placement="top" title="FullScreen">--}}
                {{--<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>--}}
            {{--</a>--}}
            {{--<a data-toggle="tooltip" data-placement="top" title="Lock">--}}
                {{--<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>--}}
            {{--</a>--}}
            {{--<a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">--}}
                {{--<span class="glyphicon glyphicon-off" aria-hidden="true"></span>--}}
            {{--</a>--}}
        {{--</div>--}}
        <!-- /menu footer buttons -->
    </div>
</div>
<!--sidebar-end-->
