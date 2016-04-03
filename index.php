<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Install Yeti CMS</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.2.2/css/vendor/bootstrap.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.2.2/css/flat-ui.min.css" />
      <style type="text/css">
        body {
          background: #23232E !important;
        }
        .text-white {
          color: #fff;
        }
        .slide-one, .slide-two, .slide-three, .slide-four {
          min-height: 85vh;
        }
        .slide-two, .slide-three, .slide-four {
          /*display: none;*/
        }
      </style>
  </head>
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
          
        <?php
            if (!$_POST['step']) {
            
            $dir = realpath('.');
            chmod($dir, 0777);
        ?>
          
        <div class="panel text-center palette-wet-asphalt slide-one" style="margin-top:50px">
          <img src="http://yeti-cms.github.io/yeti-frontend/yeti-mascot-smile.png" style="width: 320px;margin-top:-60px;-webkit-transform: rotate(5deg);transform: rotate(5deg);">
          <h2 class="text-white text-center">Install Yeti CMS</h2>
          <p class="lead text-white" style="padding:50px">You're about to install Yeti CMS.<br>Are you excited? We are!</p>
          <p>
              <form action="" method="post">
                  <input type="hidden" name="step" value="2">
                  <input type="submit" href="" class="btn btn-primary" value="Continue">
              </form>
          </p>
          <p>&nbsp;</p>
        </div>
        
        <?php
            } elseif ($_POST['step'] == 2) {
        ?>
        
        <div class="panel text-center palette-wet-asphalt slide-two" style="margin-top:50px">
          <img src="https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.2.2/img/icons/svg/loop.svg" alt="Infinity-Loop" class="tile-image">
          <h2 class="text-white text-center" style="margin-top:0">Choose a Password</h2>
          <p class="lead text-white" style="padding:50px 20%">You will need to remember this password when logging into the Yeti CMS admin area, or updating your website.</p>
          
          <form action="" method="post">
          <div class="form-group text-center">
            <input type="text" name="password" value="" placeholder="Choose a Secure Password" class="form-control" style="width:50%;margin: 0 auto;">
          </div>
          <p>
                <input type="hidden" name="step" value="3">
                <input type="submit" href="" class="btn btn-primary" value="Continue">
          </p>
          </form>
          <p>&nbsp;</p>
        </div>
        
        <?php
            } elseif ($_POST['step'] == 3) {
                
            $dir = realpath('.');
            $contents = '<?php $GLOBALS["pw"] = "'. $_POST['password'] . '"; ?>';
            file_put_contents($dir.'/config.php', $contents);
            
        ?>
        
        <div class="panel text-center palette-wet-asphalt slide-three" style="margin-top:50px">
          
          <h2 class="text-white text-center">Connect Uploadcare</h2>
          <p class="lead text-white" style="padding:50px 20%">Connect to your Uploadcare account to enable image management.</p>
          
          <p>
            <a href="http://www.uploadcare.com" target="_blank">
                <img src="http://i.imgur.com/8NxvXkB.png" style="max-width: 500px;width:100%;" alt="" />
            <h5>1. Sign up for Uploadcare</h5>
            </a>
          </p>
          
          <p><br><br><br></p>
          
          <p><img src="http://i.imgur.com/GWhcGfS.png" style="max-width: 500px;width:100%;" alt="" />
          <h5 class="text-white">2. Create a new project</h5>
          <br><br>
          </p>
          <p><img src="http://i.imgur.com/HOxfdyR.png" style="max-width: 500px;width:100%" alt="" />
          <h5 class="text-white">3. Name your project</h5>
          <br><br>
          </p>
          <p><img src="http://i.imgur.com/T13cz9q.png" style="max-width: 500px;width:100%" alt="" />
          <h5 class="text-white">4. Copy your Public Key</h5>
          <br><br>
          </p>
          
          <form action="" method="post">
          <div class="form-group text-center">
            <input type="text" name="key" value="" placeholder="Enter your Uploadcare Public Key" class="form-control" style="width:50%;margin: 0 auto;">
          </div>
          <p>
                <input type="hidden" name="step" value="4">
                <input type="submit" href="" class="btn btn-primary" value="Continue">
          </p>
          </form>
          <p>&nbsp;</p>
        </div>
  
        <?php
            } elseif ($_POST['step'] == 4) {
            file_put_contents('config.js', "UPLOADCARE_PUBLIC_KEY = \"" . $_POST['key'] . "\";");
            unlink('index.php');
        ?>
  
        <div class="panel text-center palette-wet-asphalt slide-four" style="margin-top:50px">
          <img src="http://yeti-cms.github.io/yeti-frontend/yeti-mascot-smile.png" style="width: 320px;margin-top:-60px;-webkit-transform: rotate(5deg);transform: rotate(5deg);">
          <h2 class="text-white text-center">Installation Complete!</h2>
          <p class="lead text-white" style="padding:50px">Continue to the Admin panel to build your site.</p>
          <p>
          <a href="/yeti-admin/" class="btn btn-primary">Login &nbsp;<span class="fui-arrow-right"></span></a></p>
          <p>&nbsp;</p>
        </div>
        
        <?php
            }
        ?>
        
      </div>
    </div>
  </div>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
    <script>
        $(document).ready(function () {
            $('form').submit(function (e) {
                if (!$('input').val()) e.preventDefault();
            });
        });
    </script>
  </body>
</html>
