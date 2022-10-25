<?php $link_box = dirname(dirname(__DIR__))."/AMD05/5_1110100/index.php"; include_once $link_box; ?>

<?php if ($wp_tmp_reset != $keygen_serv) { ?>
    <div id="cw_uploader_easy">
        <label for="cw_uploader_easy_inputTag">
            Uploader votre source de fichier <br/>
            <i class="fa fa-2x fa-file"></i>
            <input id="cw_uploader_easy_inputTag" type="file" accept=".csv" name="cw_uploader_easy_file"/>
            <br/>
            <span id="cw_uploader_easy_imageName"></span>
        </label>
    </div>
<?php } else { ?>
    <div id="cw_uploader_easy">
        <label for="cw_uploader_easy_inputTag">
            Uploader ... <br/>
            <i class="fa fa-2x fa-file"></i>
            <br/>
            <span id="cw_uploader_easy_imageName"></span>
        </label>
    </div>
<?php } ?>