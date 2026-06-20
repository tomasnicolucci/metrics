<div class="rm-login">

    <h1>Área Cliente</h1>

    <?php
    wp_login_form([
        'redirect' =>
            home_url('/cliente/dashboard')
    ]);
    ?>

</div>