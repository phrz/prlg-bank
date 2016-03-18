<?php
    defined('INDIRECT') or die('Forbidden');
    $pageTitle = 'Log In';
    include 'header.part.php';
?>

        <div class="card login">
            <h1>Log In</h1>

            <?php if (isset($_GET['e']) && $_GET['e'] == 0): ?>
                <p style="color:red"><b>Error:</b> please check your username or
                    password.</p>
            <?php elseif (isset($_GET['e']) && $_GET['e'] == 1): ?>
                <p style="color:green">You have been logged out.</p>
            <?php elseif (isset($_GET['e']) && $_GET['e'] == 2): ?>
                <p style="color:red"><b>Error:</b> you need to be logged in to access this page.</p>
            <?php endif; ?>

            <form action="login.php" method="post">
                <input type="text" name="username" placeholder="Username" autofocus><br>
                <input type="password" name="password" placeholder="Password"><br>
                <button>Log In</button>
            </form>

        </div><!-- .card -->


<?php include 'footer.part.php'; ?>
