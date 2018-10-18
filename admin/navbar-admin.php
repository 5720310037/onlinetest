  <nav class="navbar navbar-expand-lg navbar-dark bg-info fixed-top" id="mainNav">
    <a class="navbar-brand" href="user.php">Online Testing System</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav navbar-sidenav">
        <li class="nav-item">
          <a class="nav-link" href="user.php">
            <i class="fa fa-user"></i> <span class="nav-link-text">View User</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="subject.php">
            <i class="fa fa-book"></i> <span class="nav-link-text">View Subject</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="status.php">
            <i class="fa fa-table"></i> <span class="nav-link-text">View Status</span>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
            <i class="fa fa-fw fa-user"></i> <?php echo $admin;?>
          </a>
        </li>
      </ul>
    </div>
  </nav>