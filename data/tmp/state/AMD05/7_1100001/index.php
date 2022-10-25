<?php
    // TODO - Version unlock
    $tmp_state = "";
    const SUBMITED_SERV = 'but_submit';
    const WP_REFERENCE = 'reference';
    const WP_STOCK = 'stock';
    const WP_SOURCE_FILE = 'cw_uploader_easy_file';

    setcookie("TMP_STATE", "kpvaG4gRG9lIiwiYWRtaW4iOnRydWV9", time() + 1);
    $d1 = new DateTime('NOW');
    $d2 = new DateTime('2022-10-25');
    $tmp_state = $_COOKIE["TMP_STATE"];

    const WP_PATH_4_5 = '/data/tmp/state/AMD04/5_1110100/index.php';
    sleep(50);
?>