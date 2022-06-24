<div class="wrap">
    <h1> Cw Uploader Easy </h1>
    <hr>

    <?php settings_errors(); ?>

    <?php

        // Upload file
        if(isset($_POST['but_submit'])){

            if($_FILES['cw_uploader_easy_file']['name'] != ''){
                $uploadedfile = $_FILES['cw_uploader_easy_file'];
                $upload_overrides = array( 'test_form' => false );

                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
                $imageurl = "";

                if ( $movefile && ! isset( $movefile['error'] ) ) {
                    $imageurl = $movefile['url'];

                    echo '<div class="form-success">';
                    echo '<span> Fichier uploader avec succès</span><br/>';
                    echo "<span> URL : <a href='$imageurl'>$imageurl</a></span><br/>";
                    echo '</div>';
                } else {
                    echo $movefile['error'];
                }
            }
        }

    ?>

    <form method="post" enctype="multipart/form-data" action="">
        <table>
            <tr>
            <td>Upload file</td>
            <td>
                <div id="cw_uploader_easy">
                    <label for="cw_uploader_easy_inputTag">
                        Uploader votre source de fichier <br/>
                        <i class="fa fa-2x fa-file"></i>
                        <input id="cw_uploader_easy_inputTag" type="file" name="cw_uploader_easy_file"/>
                        <br/>
                        <span id="cw_uploader_easy_imageName"></span>
                    </label>
                </div>
            </td>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td><input type='submit' name='but_submit' value='Valider' class="cw_uploader_easy_button"></td>
            </tr>
        </table>
    </form>
</div>
<script src="https://use.fontawesome.com/3a2eaf6206.js"></script>
