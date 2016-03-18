<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $pageTitle; ?></title>
        <link rel="stylesheet" href="css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

        <header>
            <a href="http://paulherz.com/bank" class="title">Bank</a>
            <?php if (isset($user)): ?>
                <nav class="h-right">
                    Welcome, <?php echo $user->username; ?>
                    <a href="logout.php" class="logout">Log Out</a>
                </nav><!-- .h-right -->
            <?php endif; ?>
        </header>
