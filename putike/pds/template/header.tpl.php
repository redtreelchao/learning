<?php define('BASE_URL', '/'); ?><header class="header navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <span class="logo navbar-brand">
                    <a class="hidden-xs" href="<?php echo BASE_URL; ?>">Product Data System 产品数据系统 <span class="label label-sm version">Beta 2.0</span></a>
                    <a class="sidebar-toggle visible-xs-inline"><span class="glyphicon glyphicon-menu-hamburger"></span></a>
                    <strong class="visible-xs-inline">PDS<em class="label label-sm version">&beta;2.0</em></strong>

                </span>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="glyphicon glyphicon-option-vertical"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse">
                <form class="navbar-form navbar-right hidden-md hidden-lg">
                    <input type="text" class="form-control" name="keyword" placeholder="Search...">
                </form>
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden-lg hidden-md"><a href="javascript:location.reload(1);">刷新</a></li>
                    <li><a href="<?php echo BASE_URL; ?>setting.php">设置</a></li>
                    <li><a href="<?php echo BASE_URL; ?>login.php?method=logout">登出</a></li>
                </ul>
            </div>
        </div>
    </header>
