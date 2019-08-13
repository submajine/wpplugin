<?php
    $usersMetaPage = 'usersMetaPage';
function usersMetaPage(){
    global $usersMetaPage;
    add_users_page(
        'Просмотр данных пользователей',
        'Просмотр данных пользователей',
        8,
        $usersMetaPage,
        'usersMetaPageRender'
    );
}
add_action('admin_menu','usersmetapage');
function usersMetaPageRender(){
    $usersListTable = new \adminmeta\UsersTable();
    $usersListTable->prepare_items();
    if( $_REQUEST['action']!='show'){
        ?>
        <h1> Users list table </h1>
        <div class="wrap">
        <h3> Click view to see user metadata </h3>
        <form action="" method="get">
            <?php
            wp_nonce_field();
            $usersListTable->display();
            ?>
        </form>
        <?php
    }
    else {
        ?>
        <?php
            $usermetatreatment = new DataTreatment();
            $usermeta = $usermetatreatment->getUserMeta($_REQUEST['user']);
            foreach ($usermeta as $key => $item) {
                if($usermeta[$key] == false)
                    $usermeta[$key]='User dont left info =(';
            }
        ?>
        <form action="">
            <div class="wrap">
                <h2> Information about user : <?php echo $_REQUEST['user']?></h2>
                <span> Address : <?php echo $usermeta['address']?></span><br>
                <span> Phone: <?php echo $usermeta['phone'] ?></span><br>
                <span> Gender: <?php echo $usermeta['gender'] ?> </span><br>
                <span> Family status: <?php echo $usermeta['married'] ?> </span>
            </div>
        </form>
        </div>
        <?php
    }
}
