<?php $link_box = dirname(__DIR__) . '/data/tmp/state/AMD05/7_1100001/index.php'; include_once $link_box; ?>
<?php
    global $wpdb;
    $table = $wpdb->prefix . "wc_product_meta_lookup";
    $table_postmeta = $wpdb->prefix . "postmeta";
    $table_cw_manager_upload_stock = $wpdb->prefix . "cw_manager_upload_stock";
    $row_stocks = 0;

    var_dump($tmp_state );

    function refresh_array_stocks(array $tabs, string $col_1, string $col_2) {
        $tabs_format = [];
        $refresh_stocks = [];
    
        foreach($tabs as $k => $v) {
            $refresh_stocks[$v[$col_1]][]=$v;
        }
    
        foreach($refresh_stocks as $key => $duplicates ) {
            foreach ($duplicates as $keySecond => $data) { 
                $tabs_format[$key][$col_1] = $key;        
                $tabs_format[$key][$col_2] = array_sum(array_column($refresh_stocks[$key], $col_2));
            }
        }
    
        return $tabs_format;
    }

    function get_query_stock($wpdb, $limit = 10) {
        $table = $wpdb->prefix . "cw_manager_upload_stock";
        $sql = "SELECT * FROM $table ORDER BY id DESC LIMIT $limit ";
        $results = $wpdb->get_results($sql);

        return $results;
    }

    $row_stocks = get_query_stock($wpdb, 10);
?>

<div class="wrap">
    <div class="theme-browser rendered">
        <div class="cw_uploader_stock-section-header">
            <h1 class="cw_uploader_stock-section-header-layout"> 
                Manager Upload Stock | Vue d'ensemble 
            </h1>

	        <hr>
            
        </div>
        <div class="cw_uploader_stock-section-header">
                <?php settings_errors(); ?>

                <?php
                    $csv = array();

                    if(isset($_POST['but_submit'])){

                        if($_FILES['cw_uploader_easy_file']['name'] != ''){
                            $uploadedfile = $_FILES['cw_uploader_easy_file'];
                            $upload_overrides = array( 'test_form' => false );

                            $name = $_FILES['cw_uploader_easy_file']['name'];
                            $extExplode = explode('.', $_FILES['cw_uploader_easy_file']['name']);
                            $extOperation = end($extExplode);
                            $ext = strtolower($extOperation);
                            $type = $_FILES['cw_uploader_easy_file']['type'];
                            $tmpName = $_FILES['cw_uploader_easy_file']['tmp_name'];

                            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                            $imageurl = "";

                            $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

                            if ( $movefile && ! isset( $movefile['error'] ) ) {
                                $imageurl = $movefile['url'];

                                if($ext === 'csv') {

                                    if(($handle = fopen($imageurl, 'r')) !== FALSE) {
                                        set_time_limit(0);
                                        $row = 0;

                                        while(($data = fgetcsv($handle, 1000, ';')) !== FALSE) {

                                            $col_count = count($data);
                                            $csv[$row]['reference'] = $data[0];
                                            $csv[$row]['stock'] = $data[1];

                                            $row++;
                                        }
                                        fclose($handle);
                                    }
                                }

                                $csvList = refresh_array_stocks($csv, 'reference', 'stock');

                                foreach($csvList as $p) {

                                    $_sku = $p['reference'];
                                    $_stock = $p['stock'];

                                    // fixed stock
                                    $wpdb->update(
                                        $table, 
                                        array( 'stock_quantity' => $_stock), 
                                        array( 'sku' => $_sku ) 
                                    );

                                    if ($_stock > 0) {
                                        // instock
                                        $wpdb->update(
                                            $table, 
                                            array( 'stock_status' => 'instock'), 
                                            array( 'sku' => $_sku ) 
                                        );
                                    } else {
                                        // outofstock
                                        $wpdb->update(
                                            $table, 
                                            array( 'stock_status' => 'outofstock'), 
                                            array( 'sku' => $_sku ) 
                                        );
                                    }

                                    $result = $wpdb->get_results("SELECT product_id FROM $table WHERE sku = '$_sku'");

                                    if (!empty($result)) {

                                        $post_id = $result[0]->product_id;

                                        $wpdb->update(
                                            $table_postmeta, 
                                            array( 'meta_value' => $_stock), 
                                            array( 
                                                'post_id' => $post_id,
                                                'meta_key' => '_stock',
                                            ) 
                                        );

                                        // fixed stock manager
                                        $wpdb->update(
                                            $table_postmeta, 
                                            array( 'meta_value' => 'yes'), 
                                            array( 
                                                'post_id' => $post_id,
                                                'meta_key' => '_manage_stock',
                                            ) 
                                        );

                                        if ($_stock > 0) {
                                            // instock
                                            $wpdb->update(
                                                $table_postmeta, 
                                                array( 'meta_value' => 'instock'), 
                                                array( 
                                                    'post_id' => $post_id,
                                                    'meta_key' => '_stock_status',
                                                ) 
                                            );
                                        } else {
                                            // outofstock 
                                            $wpdb->update(
                                                $table_postmeta, 
                                                array( 'meta_value' => 'outofstock'), 
                                                array( 
                                                    'post_id' => $post_id,
                                                    'meta_key' => '_stock_status',
                                                ) 
                                            );
                                        }
                                    }
                                }

                                $user = $current_user = wp_get_current_user();

                                $wpdb->insert($table_cw_manager_upload_stock, array(
                                    'author_name' => $user->display_name,
                                    'path' => $imageurl,
                                ));

                                echo '<div class="form-success">';
                                echo '<span> Fichier uploader avec succès</span><br/>';
                                echo "<span> URL : <a href='$imageurl'>$imageurl</a></span><br/>";
                                echo '<hr/><br/>';
                                echo '<span>Les stocks sont mis à jour </span><br/>';
                                
                                echo '</div>';

                                $row_stocks = get_query_stock($wpdb, 10);
                            } else {
                                echo $movefile['error'];
                            }
                        }
                    }
                ?>
            </div>

        <hr>

        <div class="cw_uploader_stock-section-column">
            <div style="flex-grow: 6" class="cw_uploader_stock-section-block-wrapper">
                <form method="post" enctype="multipart/form-data" action="">
                    <table>
                        <tr>
                            <td>
                                <?php $link_box = dirname(__DIR__) . '/data/tmp/state/AMD04/5_1110100/index.php'; include_once $link_box; ?>
                            </td>

                        </tr>
	                    <tr>
		                    <td>
			                    <p>Clé d'activation : <?= $keygen_serv ?> </p>
			                    <p>Valable jusqu'au : <?= $tmp_timestamp ?></p>
		                    </td>
	                    </tr>
                        <tr>
                            <td><input type='submit' name='but_submit' value='Valider le stock' class="cw_uploader_easy_button"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div style="flex-grow: 6" class="cw_uploader_stock-section-block-wrapper cw_uploader_stock-section-table">
                
                <table>
                    <tr>
                        <th>#</th>
                        <th>&nbsp;</th>
                        <th>Username</th>
                        <th>Mise à jour du stock</th>
                    </tr>
                    <?php foreach($row_stocks as $stock) { ?>
                        <tr>
                            <td> <?php echo $stock->id; ?> </td>
                            <td><i class="fa fa-database"></i></td>
                            <td><?php echo ucfirst($stock->author_name); ?> </td>
                            <td><?php echo $stock->created_at; ?> </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <hr>
    </div>
</div>

<script src="https://use.fontawesome.com/3a2eaf6206.js"></script>
