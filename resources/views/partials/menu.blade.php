<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/img/lte/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <!-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form> -->
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu left-menu">
            <li class="header">MAIN MENU</li>
            <li class="dashboard">
                <a href="/">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-key"></i>
                    <span>IAM</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/users"><i class="fa fa-circle-o"></i>Users</a></li>
                    <li><a href="/users/roles"><i class="fa fa-circle-o"></i>Roles</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-university"></i>
                    <span>Merchants</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/merchants"><i class="fa fa-circle-o"></i>Merchants List</a></li>
                    <!-- <li><a href="/merchants/user"><i class="fa fa-circle-o"></i>Merchants Users</a></li> -->
                </ul>
            </li>

            <li class="ses">
                <a href="/ses">
                    <i class="fa fa-money"></i> <span>Socio Economic Status</span>
                </a>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-sticky-note"></i> <span>Questionnaire</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/questionnaire"><i class="fa fa-circle-o"></i>Questionnaire</a></li>
                    <li><a href="/questions"><i class="fa fa-circle-o"></i>Questions</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cogs"></i> <span>Settings</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/settings"><i class="fa fa-circle-o"></i>General</a></li>
                    <li><a href="/promotions"><i class="fa fa-circle-o"></i>Promotion</a></li>
                    <li><a href="/lucky"><i class="fa fa-circle-o"></i>Lucky Draw</a></li>
                    <li><a href="/points"><i class="fa fa-circle-o"></i>Points</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-history"></i> <span>History</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/history/transactions"><i class="fa fa-circle-o"></i>Transactions</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="/report">
                    <i class="fa fa-file"></i> <span>Report</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>

            <li class="header">LABELS</li>
            <!-- <li class="header">LABELS</li>
            <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
            <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
            <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li> -->
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
