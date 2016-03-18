<?php
    defined('INDIRECT') or die('Forbidden');
    $pageTitle = 'Accounts';
    include 'header.part.php';
?>

        <main class="card">
            <h1>Accounts</h1>

            <!-- balance -->
            <table>
                <thead><th>Account</th><th>Balance</th></thead>
                <tbody>
                    <?php foreach ($user->accounts as $account) {
    echo '<tr><td class="mono"># '.$account->number.'</td>';
    echo '<td class="right">'.money_format('$ %i', $account->balance).'</td></tr>';
} ?>
                </tbody>
            </table>


            <!-- withdraw -->
            <fieldset>
                <legend>Withdraw</legend>

                <form action="withdraw.php" method="post">
                    <div class="input mono">
                        <span class="prefix">$</span>
                        <input class="right" type="text" placeholder="0.00" style="width:4em" name="amount">
                    </div><!-- .input.mono -->

                    <span style="margin: 0 5px;"> from account </span>

                    <div class="input mono">
                        <span class="prefix">#</span>
                        <select name="fromAccount" class="right" style="width: 4em">
                            <?php foreach ($user->accounts as $account) {
    echo '<option>'.$account->number.'</option>';
} ?>
                        </select>
                    </div>

                    <input
                        type="hidden"
                        name="nonce"
                        value="<?php echo NonceManager::generate($user)->nonce; ?>"
                    >

                    <button>Go</button>

                    <?php
                        if (isset($_GET['e'])) {
                            if ($_GET['e'] == 'w0') {
                                echo '<span style="color:green">Successfully withdrew from account.</span>';
                            } elseif ($_GET['e'] == 'w1') {
                                echo '<span style="color:red">There was a problem with your withdrawal.</span>';
                            } elseif ($_GET['e'] == 'w2') {
                                echo '<span style="color:red">Insufficient funds.</span>';
                            }
                        }
                    ?>
                </form>

            </fieldset>


            <!-- deposit -->
            <fieldset>
                <legend>Deposit</legend>

                <form action="deposit.php" method="post">
                    <div class="input mono">
                        <span class="prefix">$</span>
                        <input class="right" type="text" placeholder="0.00" style="width:4em" name="amount">
                    </div><!-- .input.mono -->

                    <span style="margin: 0 5px;"> to account </span>

                    <div class="input mono">
                        <span class="prefix">#</span>
                        <select name="toAccount" class="right" style="width: 4em">
                            <?php foreach ($user->accounts as $account) {
    echo '<option>'.$account->number.'</option>';
} ?>
                        </select>
                    </div>

                    <input
                        type="hidden"
                        name="nonce"
                        value="<?php echo NonceManager::generate($user)->nonce; ?>"
                    >

                    <button>Go</button>

                    <?php
                        if (isset($_GET['e'])) {
                            if ($_GET['e'] == 'd0') {
                                echo '<span style="color:green">Successfully deposited to account.</span>';
                            } elseif ($_GET['e'] == 'd1') {
                                echo '<span style="color:red">There was a problem with your deposit.</span>';
                            }
                        }
                    ?>
                </form>

            </fieldset>

            <!-- transfer -->
            <fieldset>
                <legend>Transfer</legend>

                <form action="transfer.php" method="post">
                    <div class="input mono">
                        <span class="prefix">$</span>
                        <input class="right" type="text" placeholder="0.00" style="width:4em" name="amount">
                    </div><!-- .input.mono -->

                    <!-- FROM -->
                    <span style="margin: 0 5px;"> from account </span>
                    <div class="input mono">
                        <span class="prefix">#</span>
                        <select name="fromAccount" class="right" style="width: 4em">
                            <?php foreach ($user->accounts as $account) {
    echo '<option>'.$account->number.'</option>';
} ?>
                        </select>
                    </div>

                    <!-- FROM -->
                    <span style="margin: 0 5px;"> to account </span>
                    <div class="input mono">
                        <span class="prefix">#</span>
                        <select name="toAccount" class="right" style="width: 4em">
                            <?php foreach ($user->accounts as $account) {
    echo '<option>'.$account->number.'</option>';
} ?>
                        </select>
                    </div>

                    <input
                        type="hidden"
                        name="nonce"
                        value="<?php echo NonceManager::generate($user)->nonce; ?>"
                    >

                    <button>Go</button>

                    <?php
                        if (isset($_GET['e'])) {
                            if ($_GET['e'] == 't0') {
                                echo '<span style="color:green">Successfully transferred.</span>';
                            } elseif ($_GET['e'] == 't1') {
                                echo '<span style="color:red">There was a problem with your transfer.</span>';
                            } elseif ($_GET['e'] == 't2') {
                                echo '<span style="color:red">Insufficient funds in source account.</span>';
                            } elseif ($_GET['e'] == 't3') {
                                echo '<span style="color:red">Cannot transfer to same account.</span>';
                            }
                        }
                    ?>
                </form>

            </fieldset>

        </main><!-- .card -->

        <!-- <footer>
            <span>&copy; 2016 Paul Herz</span>
        </footer> -->

<?php include 'footer.part.php'; ?>
