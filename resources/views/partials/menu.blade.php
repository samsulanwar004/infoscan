<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo env('CDN_URL', ''); ?>/img/lte/user2-160x160.jpg" class="img-circle"
                     alt="User Image">
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

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-desktop"></i> <span>Monitoring</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/crowdsource"><i class="fa fa-circle-o"></i>Crowdsource Account</a></li>
                    <li><a href="/forced-assign"><i class="fa fa-circle-o"></i>Forced assign crowdsource</a></li>
                </ul>
            </li>

            <li class="snaps">
                <a href="/snaps">
                    <i class="fa fa-picture-o"></i> <span>Snaps Management</span>
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
                    <i class="fa fa-money"></i> <span>Payment Portal</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/payment-portal"><i class="fa fa-circle-o"></i>Payment Portal</a></li>
                    <li><a href="/exchange"><i class="fa fa-circle-o"></i>Exchange Rates</a></li>
                </ul>
            </li>

            <li class="leaderboard">
                <a href="/leaderboard">
                    <i class="fa fa-list-ol"></i> <span>Leaderboard</span>
                </a>
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
                    <li><a href="/lucky"><i class="fa fa-circle-o"></i>Lucky Draw</a></li>
                    <li><a href="/product-categories"><i class="fa fa-circle-o"></i>Product Categories</a></li>
                    <li><a href="/promo-points"><i class="fa fa-circle-o"></i>Promo Points</a></li>
                    <li><a href="/promotions"><i class="fa fa-circle-o"></i>Promotion</a></li>
                    <li><a href="/ses"><i class="fa fa-circle-o"></i>Socio Economic Status</a></li>
                    <li><a href="/points"><i class="fa fa-circle-o"></i>Task Points</a></li>
                    <li><a href="/bonus-points"><i class="fa fa-circle-o"></i>Bonus Points</a></li>
                    <li><a href="/referral"><i class="fa fa-circle-o"></i>Referral Points</a></li>
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
                <a href="/reports">
                    <i class="fa fa-file"></i> <span>Reports</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/reports"><i class="fa fa-circle-o"></i>General</a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
