<?php

require('../config.php');
$pass = $_REQUEST['pw'];

function rmrf($dir) {
        if(is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file)
                rmrf("$dir/$file");
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }
    function is_recursively_deleteable($d) {
        $stack = array($d);
        while($dir = array_pop($stack)) {
            if(!is_readable($dir) || !is_writable($dir))
                return false;
            $files = array_diff(scandir($dir), array('.','..'));
            foreach($files as $file) if(is_dir($file)) {
                $stack[] = "$dir/$file";
            }
        }
        return true;
    }
    
    function err($code,$msg) {
        echo json_encode(array('error' => array('code'=>intval($code), 'msg' => $msg)));
        exit;
    }
    
    function asBytes($ini_v) {
        $ini_v = trim($ini_v);
        $s = array('g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10);
        return intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
    }

if ($pass == $pw) {
    // must be in UTF-8 or `basename` doesn't work
    setlocale(LC_ALL,'en_US.UTF-8');
    
    // if(!$_COOKIE['_sfm_xsrf'])
    //     setcookie('_sfm_xsrf',bin2hex(openssl_random_pseudo_bytes(16)));
    // if($_POST) {
    //     if($_COOKIE['_sfm_xsrf'] !== $_POST['xsrf'] || !$_POST['xsrf'])
    //         err(403,"XSRF Failure");
    // }
    
    // error_log($_POST['file']);
    $file = $_POST['file'] ?: '.';
    // error_log($file);
    
    // error_log('do ' . $_REQUEST['do']);
    
    $tmp = realpath('../' . $_POST['file']);
    // error_log($tmp);
    
    $__DIR__ = realpath('../');
    
    if ($_REQUEST['do']) {
        if($tmp === false)
            err(404,'File or Directory Not Found');
        if(substr($tmp, 0, strlen($__DIR__)) !== $__DIR__)
            err(403,"Forbidden");
    }
    
    if($_REQUEST['do'] == 'list') {
        if (is_dir($file)) {
            $directory = $file;
            $result = array();
            $files = array_diff(scandir($directory), array('.','..','.htaccess'));
            foreach($files as $entry) if($entry !== basename(__FILE__)) {
                $i = $directory . '/' . $entry;
                $stat = stat($i);
                $result[] = array(
                    'mtime' => $stat['mtime'],
                    'size' => $stat['size'],
                    'name' => basename($i),
                    'path' => preg_replace('@^\./@', '', $i),
                    'is_dir' => is_dir($i),
                    'is_deleteable' => (!is_dir($i) && is_writable($directory)) ||
                        (is_dir($i) && is_writable($directory) && is_recursively_deleteable($i)),
                    'is_readable' => is_readable($i),
                    'is_writable' => is_writable($i),
                    'is_executable' => is_executable($i),
                );
            }
        } else {
            err(412,"Not a Directory");
        }
        echo json_encode(array('success' => true, 'is_writable' => is_writable($file), 'results' =>$result));
        exit;
    } elseif ($_POST['do'] == 'delete') {
        rmrf('../'.$file);
        echo "{}";
        
        // Commit file Deletion
        // $thisDir = explode('/', $file);
        // $thisDir = $thisDir[0];
        // chdir($thisDir);
        // exec("git add . && git add -u && git commit -m \"Files Deleted\"");
        
        exit;
    } elseif ($_REQUEST['do'] == 'rename') {
        // error_log('rename ' . $_REQUEST['rename']);
        rename('../'.$file, '../'.$_REQUEST['rename']);
        echo "{}";
        
        // Commit file Deletion
        // $thisDir = explode('/', $file);
        // $thisDir = $thisDir[0];
        // chdir($thisDir);
        // exec("git add . && git add -u && git commit -m \"Files Deleted\"");
        
        exit;
    } elseif ($_POST['do'] == 'mkdir') {
        chdir($file);
        $mkdirDir = $_POST['name'];
        @mkdir($mkdirDir);
        $file = "$mkdirDir/.htaccess";
    //    file_put_contents($file, "SetHandler default-handler\n<FilesMatch \"\.(php|xml|cgi|php5|php4|php3|php2|phtml|phtm|bat|sh)$\">\ndeny from all\n</FilesMatch>\n<FilesMatch \".*\">\nOrder allow,deny \nDeny from all\n</FilesMatch>\n");
        exit;
    } elseif ($_POST['do'] == 'upload') {
        var_dump($_POST);
        var_dump($_FILES);
        var_dump($_FILES['file_data']['tmp_name']);
        $zip = new ZipArchive();
        if ($zip->open($_FILES['file_data']['tmp_name']) === TRUE) {
            $zip->extractTo('../'.$file);
            $zip->close();
            // Add .htaccesses for any directories created
            foreach ($iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator("./",
                    RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST) as $item) {
                    $subPath = $iterator->getSubPathName();
                    if($item->isDir()) {
                        $file = "$subPath/.htaccess";
                        chmod($subPath,0766);
        //                file_put_contents($file, "SetHandler default-handler\n<FilesMatch \"\.(php|xml|cgi|php5|php4|php3|php2|phtml|phtm|bat|sh)$\">\ndeny from all\n</FilesMatch>\n<FilesMatch \".*\">\nOrder allow,deny \nDeny from all\n</FilesMatch>\n");
                    }
            }
            echo "ok"; // Superfluous
        } else {
            var_dump(move_uploaded_file($_FILES['file_data']['tmp_name'], '../'.$file.'/'.$_FILES['file_data']['name']));
        }
        
        // Commit files Uploaded
        // $thisDir = explode('/', $file);
        // $thisDir = $thisDir[0];
        // chdir($thisDir);
        // exec("git add . && git add -u && git commit -m \"Files-Uploaded\"");
        
        exit;
    } elseif ($_GET['do'] == 'download') {
        $filename = basename($file);
        header('Content-Type: ' . mime_content_type($file));
        header('Content-Length: '. filesize($file));
        header(sprintf('Content-Disposition: attachment; filename=%s',
            strpos('MSIE',$_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\"" ));
        ob_flush();
        readfile($file);
        exit;
    }
    
    $MAX_UPLOAD_SIZE = min(asBytes(ini_get('post_max_size')), asBytes(ini_get('upload_max_filesize')));

}


?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>Yeti file browser</title>


    <!-- Include our stylesheets -->
    <link href="assets/css/styles.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/yeti-cms/vex.css">
    <link rel="stylesheet" href="/yeti-cms/vex-theme-default.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/messenger/1.4.0/css/messenger.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/messenger/1.4.0/css/messenger-theme-air.css">
    
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
    <link rel="stylesheet" href="/yeti-cms/yeti-cms.css">
    
    <style type="text/css">
        div#dropZone {
          background: gray;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          z-index: 999;
          opacity: 0.6;
          visibility: hidden;
        }
        .progress {
            height: 5px;
            background: #44AFA6;
            width: 0;
        }
        .yeti-context-menu {
            position: fixed; 
            top: 25%; 
            left: 36%;
            z-index:999999999;
            width: 160px !important;
            display: none;
        }
        body div.yeti-context-menu a.yeti-context-menu-button.yeti-hidden {
            display: none !important;
        }
        #uploader {
            display: none;
        }
    </style>
    
</head>
<body>
    <div id="progress">
        <div class="progress"></div>
    </div>
    
    <a onclick="login()" style="cursor: pointer;font-family:sans-serif;text-decoration:none;color:#fff;position:fixed;top:50px;right:40px">LOGIN</a>

    <div class="filemanager">

        <div class="search">
            <input type="search" placeholder="Find a file.." />
        </div>

        <div class="breadcrumbs"></div>

        <ul class="data"></ul>

        <div class="nothingfound">
            <div class="nofiles"></div>
            <span>No files here.</span>
        </div>

    </div>
    
    <div id="uploader" style="
    width: 500px;
    border: 3px dashed rgba(255,255,255,0.25);
    text-align: center;
    border-radius: 7px;
    margin: 0 auto;
    padding:  50px;
    font-size: 18px;
    color: rgba(255,255,255,0.25);
">Drag files on screen to upload or<br>
<input type="file" multiple="" style="display:block;margin:0 auto;padding-left: 75px;padding-top:10px">
</div>
    
    <div class="yeti-context-menu">
        <a class="yeti-context-menu-button new-folder">
          <i class="ion-ios-folder-outline" style="padding-right:7px !important"></i>
          New Folder
        </a>
        <a class="yeti-context-menu-button new-file">
          <i class="ion-document" style="padding-right:12px !important"></i>
          New File
        </a>
        <a class="yeti-context-menu-button edit-code">
          <i class="ion-code-working" style="padding-right:7.5px !important"></i>
          Edit Code
        </a>
        <a class="yeti-context-menu-button download">
          <i class="ion-ios-download-outline" style="padding-right:11px !important"></i>
          Download
        </a>
        <a class="yeti-context-menu-button clone-file">
          <i class="ion-ios-copy-outline" style="padding-right:12px !important"></i>
          Clone File
        </a>
        <a class="yeti-context-menu-button rename">
          <i class="ion-android-textsms" style="padding-right:9px !important"></i>
          Rename
        </a>
        <a class="yeti-context-menu-button delete">
          <i class="ion-backspace-outline" style="padding-right:6px !important"></i>
          Delete
        </a>
      </div>
    
    <div id="dropZone"></div>

    <!-- Include our script files -->
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="assets/js/script.js"></script>

    <script src="/yeti-cms/vex.combined.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/messenger/1.4.0/js/messenger.min.js"></script>
    
    <script class='yeti-script'>vex.defaultOptions.className = 'vex-theme-default'; Messenger.options = { extraClasses: 'messenger-fixed messenger-on-top messenger-on-right', theme: 'air'};</script>

    <script>
        var dropZone = document.getElementById('dropZone');

        function showDropZone() {
            dropZone.style.visibility = "visible";
        }
        function hideDropZone() {
            dropZone.style.visibility = "hidden";
        }
        
        function allowDrag(e) {
            if (true) {  // Test that the item being dragged is a valid one
                e.dataTransfer.dropEffect = 'copy';
                e.preventDefault();
            }
        }
        
        function handleDrop(e) {
            e.preventDefault();
            hideDropZone();
            
            // console.log(e);
        
            // alert('Drop!');
            var files = e.dataTransfer.files;
            $.each(files,function(k,file) {
                uploadFile(file);
            });
        }
        
        // 1
        window.addEventListener('dragenter', function(e) {
            showDropZone();
        });
        
        // 2
        dropZone.addEventListener('dragenter', allowDrag);
        dropZone.addEventListener('dragover', allowDrag);
        
        // 3
        dropZone.addEventListener('dragleave', function(e) {
            hideDropZone();
        });
        
        // 4
        dropZone.addEventListener('drop', handleDrop);
        
        function decodeHtmlEntities (string) {
            return $('<textarea />').html(string).text();
        }
        
        function uploadFile (file) {
            var folder = window.location.hash.substr(1);

            // if(file.size > MAX_UPLOAD_SIZE) {
            //         var $error_row = renderFileSizeErrorRow(file,folder);
            //         $('#upload_progress').append($error_row);
            //         window.setTimeout(function(){$error_row.fadeOut();},5000);
            //         return false;
            // }

            var fd = new FormData();
            fd.append('file_data', file);
            folder = decodeURIComponent(folder).substr(2);
            fd.append('file', folder);
            console.log(folder);
            // fd.append('xsrf', XSRF);
            fd.append('do','upload');
            fd.append('pw', localStorage.getItem('_yeti_pw'));
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '');
            xhr.onload = function() {
                $('.progress').hide();
                loadFiles();
            };
            xhr.upload.onprogress = function(e){
                if(e.lengthComputable) {
                    $('.progress').css('width',(e.loaded/e.total*100 | 0)+'%' );
                }
            };
            xhr.send(fd);
        }
        
        $('#file_drop_target').bind('drop', function (e) {
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            $.each(files,function(k,file) {
                uploadFile(file);
            });
        });
        
        $('input[type=file]').change(function (e) {
            e.preventDefault();
            $.each(this.files,function (k, file) {
                uploadFile(file);
            });
        });
        
        $(document).ready(function () {
            $('ul.data').on('contextmenu', 'li', function (event) {
                event.preventDefault();
                
                // Displays the context menu near the mouse pointer
                var xOffset = event.clientX;
                var yOffset = event.clientY;
        
                // Prevent context menu from being rendered too close to the right edge of the window
                if (xOffset > $(window).width() - 160)
                    xOffset = parseInt($(window).width() - 160);
        
                // Prevent context menu from being rendered too close to the bottom edge of the window
                if (yOffset > $(window).height() - 50)
                    yOffset = parseInt(yOffset - 50);
        
                $('.yeti-context-menu').css('top', yOffset + 'px');
                $('.yeti-context-menu').css('left', xOffset + 'px');
                $('.yeti-context-menu-button').addClass('yeti-hidden');
                
                var pathName = $(this).find('a').attr('href');
                var pn = pathName.split('.././').join('');
                if (pathName.indexOf('.html') !== -1 || pathName.indexOf('.htm') !== -1) {
                    $('.yeti-context-menu-button.edit-code').removeClass('yeti-hidden');
                    $('.yeti-context-menu-button.edit-code').attr('href', '../editor.php?path=' + pn);
                }
                $('.yeti-context-menu-button').attr('data-path', pn);
                
                $('.yeti-context-menu-button.rename, .yeti-context-menu-button.delete').removeClass('yeti-hidden');
                $('.yeti-context-menu').show();
                
            });
            $('body, body *, ul.data, ul.data *').click(function () {
                $('.yeti-context-menu').hide();
            });
            
            $('.yeti-context-menu-button.rename').click(function () {
                var pathName = $(this).attr('data-path').split('../').join('').split('./').join('');
                var arr = pathName.split('/');
                var fName = arr.pop();
                var path = arr.join('/');
                vex.dialog.prompt({
                    message: 'Rename File',
                    value: fName,
                    callback: function(value) {
                        var rename = (path || '.') + "/" + value;
                        $.post("", { do: 'rename', file: pathName, rename: rename, pw: localStorage.getItem('_yeti_pw') }, function (response) {
                            loadFiles();
                        },'json');
                    }
                });
            });
            
            $('.yeti-context-menu-button.delete').click(function () {
                var pathName = $(this).attr('data-path').split('../').join('').split('./').join('');
                $.post("", { 'do':'delete', file: pathName, pw: localStorage.getItem('_yeti_pw') } , function (response) {
                    loadFiles();
                },'json');
            });
        });
    </script>
</body>
</html>