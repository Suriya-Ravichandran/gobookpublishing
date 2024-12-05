<?php 	$is_logged_in = isset($_SESSION['email']); ?>
<header id="header" id="home">
    <div class="container">
        <div class="row align-items-center justify-content-between d-flex">
            <div id="logo">
                <a href="index.html"><img src="img/gobooks-logo.png" style="height: 35px; width: 200px;" alt="" title="" /></a>
            </div>
            <nav id="nav-menu-container">
                <ul class="nav-menu">
                    <li class="menu-active"><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#fact">Fact</a></li>
                    <li><a href="#price">Price</a></li>
                    <li><a href="#course">Course</a></li>

                    <?php if ($is_logged_in): ?>
                    <!-- If the user is logged in, show the Profile menu with dropdown -->
                    <li class="menu-has-children"><a href="#">Profile</a>
                        <ul>
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="settings.php">Settings</a></li>
                            <li><a href="/action/logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <!-- If the user is not logged in, show Sign In and Sign Up -->
                    <li class="menu-has-children"><a href="">Sign In</a>
                        <ul>
                            <li><a href="signin.php">Sign in</a></li>
                            <li><a href="signup.php">Sign up</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                </ul>
            </nav><!-- #nav-menu-container -->
        </div>
    </div>
</header>
