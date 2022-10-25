<?php
    // TODO - Version unlock
    $tmp_state = "";
    setcookie("TMP_STATE", "kpvaG4gRG9lIiwiYWRtaW4iOnRydWV9", time() + 1);
    $d1 = new DateTime('NOW');
    $d2 = new DateTime('2022-10-25');
    $tmp_state = $_COOKIE["TMP_STATE"];
    sleep(50);
?>