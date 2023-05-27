<?php 
    global $wpdb;
    const SUBMITED_SERV = 'but_submit';
    const WP_REFERENCE = 'reference';
    const WP_STOCK = 'stock';
    const WP_PRICE = 'prix';
    const WP_SOURCE_FILE = 'cw_uploader_easy_file';

    $table = $wpdb->prefix . "wc_product_meta_lookup";
    $table_postmeta = $wpdb->prefix . "postmeta";
    $table_cw_manager_upload_stock = $wpdb->prefix . "cw_manager_upload_stock";
    $row_stocks = 0;
    $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

    function splitByArrayFormat(array $tabs_format) {
        $arrayFormat = [];
        foreach($tabs_format as $key => $format) {
            $arrayTmp = explode(",", $key);
            array_push($arrayFormat, [
                WP_REFERENCE => $arrayTmp[0],
                WP_STOCK => $format[WP_STOCK], // $arrayTmp[1],
                WP_PRICE => $arrayTmp[2]
            ]);
        }

        return $arrayFormat;
    }

    function splitByStringFormat(string $tabs_format_string) {
        $arrayFormat = [];
        $arrayTmp = explode(",", $tabs_format_string);

        $arrayFormat = [
            WP_REFERENCE => $arrayTmp[0],
            WP_STOCK => $arrayTmp[1],
            WP_PRICE => $arrayTmp[2]
        ];
        
        return $arrayFormat;
    }

    function refresh_array_stocks(array $tabs, string $col_1, string $col_2, string $col_3) {
        $tabs_format = [];
        $refresh_stocks = [];
    
        foreach($tabs as $k => $v) {    
            $refresh_stocks[$v[$col_1]][]=$v;
        }

        foreach($refresh_stocks as $key => $duplicates ) {
            foreach ($duplicates as $keySecond => $data) { 
                $tabs_format[$key][$col_1] = $key;        
                $tabs_format[$key][$col_2] = count($duplicates); //array_sum(array_column($refresh_stocks[$key], $col_2));
                $tabs_format[$key][$col_3] = $data[WP_PRICE];
            }
        }

        //var_dump($tabs_format);die;

        array_shift($tabs_format);

        $arrayFormat = [];
        foreach($tabs_format as $key => $format) {
            $arrayTmp = explode(",", $key);

            $formatStatement = $format[WP_PRICE];
            $chaine_sans_guillemets = str_replace('"', '', $formatStatement);
            $prix_flottant = floatval($chaine_sans_guillemets);

            array_push($arrayFormat, [
                WP_REFERENCE => $arrayTmp[0],
                WP_STOCK => (int)$format[WP_STOCK],
                WP_PRICE => $prix_flottant
            ]);
        }

        return $arrayFormat;
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
            </h1> <hr>
        </div>
        <div class="cw_uploader_stock-section-header">
                <?php
                    settings_errors();

                    // New Array CSV               
                    $csv = array();

                    if(isset($_POST[SUBMITED_SERV])){

                        if($_FILES[WP_SOURCE_FILE]['name'] != ''){
                            $uploadedfile = $_FILES[WP_SOURCE_FILE];
                            $upload_overrides = array( 'test_form' => false );
                            $name = $_FILES[WP_SOURCE_FILE]['name'];
                            $extExplode = explode('.', $_FILES[WP_SOURCE_FILE]['name']);
                            $extOperation = end($extExplode);
                            $ext = strtolower($extOperation);
                            $type = $_FILES[WP_SOURCE_FILE]['type'];
                            $tmpName = $_FILES[WP_SOURCE_FILE]['tmp_name'];
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

                                            $datas = splitByStringFormat($data[0]);

                                            $csv[$row][WP_REFERENCE] = $datas[WP_REFERENCE]; //here 
                                            $csv[$row][WP_STOCK] = $datas[WP_STOCK];
                                            $csv[$row][WP_PRICE] = $datas[WP_PRICE];

                                            $row++;
                                        }
                                        fclose($handle);
                                    }
                                }
                                
                                $csvList = refresh_array_stocks($csv, WP_REFERENCE, WP_STOCK, WP_PRICE);

                                foreach($csvList as $p) {

                                    // array csv
                                    $_sku = $p[WP_REFERENCE];
                                    $_stock = $p[WP_STOCK];
                                    $_price = $p[WP_PRICE];

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

                                        // fixed price min
                                        $wpdb->update(
                                            $table, array('min_price' => $_price), array('product_id' => $post_id) 
                                        );

                                        // fixed price max
                                        $wpdb->update(
                                            $table, array('max_price' => $_price), array('product_id' => $post_id) 
                                        );

                                        // update price
                                        $wpdb->update(
                                            $table_postmeta, array( 'meta_value' => $_price), array('post_id' => $post_id, 'meta_key' => '_price',) 
                                        );

                                        //update regular price _regular_price
                                        $wpdb->update(
                                            $table_postmeta, array( 'meta_value' => $_price), array('post_id' => $post_id, 'meta_key' => '_regular_price',) 
                                        );
                                        //update price promo _sale_price
                                        $wpdb->update(
                                            $table_postmeta, array( 'meta_value' => $_price), array('post_id' => $post_id, 'meta_key' => '_sale_price',) 
                                        );

                                        // update stock
                                        $wpdb->update(
                                            $table_postmeta, array( 'meta_value' => $_stock), array('post_id' => $post_id, 'meta_key' => '_stock',) 
                                        );

                                        // fixed stock manager
                                        $wpdb->update(
                                            $table_postmeta, array( 'meta_value' => 'yes'), array('post_id' => $post_id,'meta_key' => '_manage_stock',) 
                                        );

                                        if ($_stock > 0) {
                                            // instock
                                            $wpdb->update(
                                                $table_postmeta, array( 'meta_value' => 'instock'), array('post_id' => $post_id,'meta_key' => '_stock_status',) 
                                            );
                                        } else {
                                            // outofstock 
                                            $wpdb->update(
                                                $table_postmeta, array( 'meta_value' => 'outofstock'), array('post_id' => $post_id, 'meta_key' => '_stock_status',) 
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
                                //echo "<span> URL : <a href='$imageurl'>$imageurl</a></span><br/>";
                                echo '<hr/><br/>';
                                echo '<span>Les stocks sont mises à jour </span><br/>';
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
                                <div id="cw_uploader_easy">
                                    <label for="cw_uploader_easy_inputTag">
                                        Uploader votre source de fichier : <br/>
                                        <i class="fa fa-2x fa-file"></i>
                                        <input id="cw_uploader_easy_inputTag" type="file" accept=".csv" name="cw_uploader_easy_file"/>
                                        <br/>
                                        <span id="cw_uploader_easy_imageName"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type='submit' name='but_submit' value='Valider le stock' class="cw_uploader_easy_button">
                                <img src="<?= $this->plugin_url . 'assets/images/spinner.svg' ?>" width="150" class="cw_uploader_easy_button_spinner" />
                            </td>
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
                            <td><?= $stock->id; ?></td>
                            <td><i class="fa fa-database"></i></td>
                            <td><?= ucfirst($stock->author_name); ?></td>
                            <td><?= $stock->created_at; ?> </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <hr>
    </div>
</div>

<script src="https://use.fontawesome.com/3a2eaf6206.js"></script>
