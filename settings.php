
    <div class="wrap">
      <div class="container-pimex" style="margin-bottom:10px;">
        <div class="logo-pimex">
          <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/logo_head.png';?>"><br />
          <span>Wordpress Plugin</span>
        </div>
        <form class="optionForm" id="formPimex" method="post" action="options.php">

          <?php settings_fields( 'pimex-settings-group' ); ?>
          <?php do_settings_sections( 'pimex-settings-group' ); ?>
          <?php $forms = pmx_getform(); ?>

          <!-- Section Access -->

          <h3><?php _e( 'Access', 'pimex' ) ?></h3>
            <p><?php _e( 'Project ID:', 'pimex' ) ?><br />
               <input type="text" name="projectId" value="<?php echo esc_attr( get_option('projectId') ); ?>" /><br />
            </p>

            <p><?php _e( 'Access Token:', 'pimex' ) ?><br />
                <input type="text" name="accessToken" size="30" value="<?php echo esc_attr( get_option('accessToken') ); ?>" /><br />
            </p><br />

              <?php submit_button();  ?>
          </form>
          </div>
          <p style="text-align:center">
            <a href="http://app.pimex.co" target="_blank"><?php _e('Go to my Pimex account', 'pimex');?></a></p>
        </div>
