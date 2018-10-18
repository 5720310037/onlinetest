  <nav class="navbar navbar-expand-lg navbar-dark bg-info fixed-top" id="mainNav">
    <a class="navbar-brand" href="test-list.php">Online Testing System</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav navbar-sidenav">
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
          <a class="nav-link" href="test-list.php">
            <i class="fa fa-fw fa-edit"></i> <span class="nav-link-text">Quizzes</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
          <a class="nav-link" href="student-list.php">
            <i class="fa fa-fw fa-users"></i> <span class="nav-link-text">Students</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
          <a class="nav-link" href="report-list.php">
            <i class="fa fa-fw fa-table"></i>
            <span class="nav-link-text">Reports</span>
          </a>
        </li>
        <?php 
          if($manage == 1) {
            echo '
              <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseMulti" 
                  data-parent="#exampleAccordion">
                  <i class="fa fa-fw fa-database"></i>
                  <span class="nav-link-text">Warehouse</span>
                </a>
                <ul class="sidenav-second-level collapse" id="collapseMulti">
                  <li>
                    <a href="group-view.php"><i class="fa fa-user"></i> Groups</a>
                  </li>
                  <li>
                    <a href="subject.php"><i class="fa fa-book"></i> Subjects</a>
                  </li>
                  <li>
                    <a href="testing.php"><i class="fa fa-file-text-o"></i> Testings</a>
                  </li>
                </ul>
              </li>';
          }
        ?>
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
            <i class="fa fa-fw fa-user"></i><?php echo $lec_name;?>
          </a>
        </li>
      </ul>
    </div>
  </nav>