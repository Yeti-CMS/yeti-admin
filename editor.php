<?php

$path = $_GET['path'];
$contents = file_get_contents($path);
$contents = htmlentities($contents);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yeti Code Editor</title>
    <style>
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>
</head>
<body>

<div id="editor"><?= $contents; ?></div>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ace.js"></script>
<script src="//cloud9ide.github.io/emmet-core/emmet.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ext-emmet.js"></script>

<script>
    var path = "<?= $path; ?>";
    path = path.trim();

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/html");

    editor.setOption("enableEmmet", true);

    $(window).bind('keydown', function(event) {
        if (event.ctrlKey || event.metaKey) {
            switch (String.fromCharCode(event.which).toLowerCase()) {
                case 's':
                    event.preventDefault();

                    // Get the current text
                    var newText = editor.getValue();
                    
                    var pw = localStorage.getItem('_yeti_pw');

                    // Post to save
                    $.post( "write-file.php", { path: path, data: newText, password: pw } );

                    break;
                case 'f':
                    event.preventDefault();
                    break;
                case 'g':
                    event.preventDefault();
                    break;
            }
        }
    });
</script>

</body>
</html>