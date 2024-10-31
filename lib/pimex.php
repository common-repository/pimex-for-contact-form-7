<?php
/**
 * PimexAsync Class
 */

class PimexAsync {
  public function cf7AddIdAttr($a) {
    $wpcf = WPCF7_ContactForm::get_current();
    $pimexId = get_post_meta($wpcf->id(), '_pmxId', true);
    $pmxToken = get_post_meta($wpcf->id(), '_pmxtoken', true);

    return 'pmx-'.$pimexId.'_'.$pmxToken;
  }

  public function cf7AddHiddenFields( $array ) {
    $wpcf = WPCF7_ContactForm::get_current();

    $pimexId = get_post_meta($wpcf->id(), '_pmxId', true);
    $pmxToken = get_post_meta($wpcf->id(), '_pmxtoken', true);

    return array(
      '_pmxId' => ($pimexId) ? $pimexId : get_option('pmx_board_id'),
      '_pmxToken' => ($pmxToken) ? $pmxToken : get_option('pmx_board_token')
    );
  }

  public function cf7SaveCallback($post_id) {
    $post = get_post($post_id);

    if ($post->post_type === 'wpcf7_contact_form') {
      $pmxId = ($_POST['_pimexId']) ? sanitize_text_field($_POST['_pimexId']) : null;
      $pmxToken = ($_POST['_pimexToken']) ? sanitize_text_field($_POST['_pimexToken']) : null;
      $c_pmxId = get_post_meta($post_id, '_pmxId', true);
      $c_pmxToken = get_post_meta($post_id, '_pmxtoken', true);

      if ($pmxId !== $c_pmxId) {
        update_post_meta( $post_id, '_pmxId',  $pmxId);
      }

      if ($pmxToken !== $c_pmxToken) {
        update_post_meta( $post_id, '_pmxtoken', $pmxToken);
      }

      update_option('pmx_board_data', [
        'id' => $pmxId,
        'token' => $pmxToken
      ]);
    }
  }

  public function tabCallback(){
  	$wpcf = WPCF7_ContactForm::get_current();
    $pmxId = (get_post_meta($wpcf->id(), '_pmxId', true)) ? get_post_meta($wpcf->id(), '_pmxId', true) : '';
    $pmxToken = (get_post_meta($wpcf->id(), '_pmxtoken', true)) ? get_post_meta($wpcf->id(), '_pmxtoken', true) : ''; ?>

 		<div class="container-pimex" style="margin-bottom:10px;">
 			<div class="logo-pimex">
 				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../images/logo_head.png';?>"><br />
 				<span>Wordpress Plugin</span>
 			</div>
 			<p><?php _e('Note: Pimex require that youll replace the names of the fields in the form by: name, email, phone (if you have), and message.', 'pimex');?></p>
 				<p><label for="_pimexId">ID</label><br>
 				<input type="text" class="pxm-field" name="_pimexId" value="<?= $pmxId; ?>"></p>
 				<p><label for="_pimexToken">Token</label><br />
 				<input type="text" class="pxm-field" name="_pimexToken" value="<?= $pmxToken; ?>"></p>
 			</div>
 			<p style="text-align:center">
 				<a href="http://app.pimex.co" target="_blank"><?php _e('Go to my Pimex account', 'pimex');?></a>
 			</p>
  <?php }

  public function cf7AddTab( $panels ) {

      $panels['pimex'] = array(
  			'title'     => 'Pimex',
  			'callback'  => Array($this, 'tabCallback')
  		);

      return $panels;
  }

  public function asyncScript () { ?>
    <!-- Pimex code integration -->
      <script>
       !function(e,n,t,c,y,s,r,u){s=n.createElement(t),r=n.getElementsByTagName(t)[0],
       s.async=!0,s.src=c,r.parentNode.insertBefore(s,r),
       s.onload = function () {Pimex.init(y, false)}}
       (window,document,'script','//statics.pimex.co/services/async.js', false);
      </script>
  <? }

  public function cf7EventScript () { ?>
    <!-- Pimex ContactForm event watch wpcf7mailsent -->
    <script>
      document.addEventListener( 'wpcf7submit', function( event ) {
        // Pimex.test().async()
        var formFields = event.detail.inputs
        var formElement = jQuery(event.target).find('form')

        function getPmxDataByForm (form) {
          var formId = form.attr('id')
          var pmxData = formId.split('-')[1].split('_')

          return {
            id: pmxData[0],
            token: pmxData[1]
          }
        }

        function formatData (fields) {
          var data = {
            custom: {}
          }

          for (i in fields) {
            data[fields[i].name] = fields[i].value;
          }

          return data
        }

        Pimex.async(formatData(formFields), getPmxDataByForm(formElement), function (err, res) {
            if(err) return console.log(err)

           console.log(res)
        })

      }, false );
      </script>
    <!-- end Pimex code -->
  <? }

  public function langPlugin () {
    // load languages for plugin
    $domain = 'pimex';
    $plugin_path = dirname(plugin_basename( __FILE__ ) .'/lang/' );

    load_plugin_textdomain( $domain, false, plugin_basename( dirname( __FILE__ ) ) . '/lang/' );
  }

  public function cf7ErrorNotice () { ?>
    <div class="error">
      <p><?php _e('To use <b>Pimex</b> it is necessary that you have installed and activated the plugin', 'pimex');?>
        <a href="<?= get_bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=search&s=contact+form+7">Contact Form 7</a>
      </p>
    </div>
  <? }

  public function validCf7 () {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
      add_action('admin_notices', Array($this, 'cf7ErrorNotice'));

      deactivate_plugins( plugin_basename( __FILE__ ) );

      if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
      }
    }
  }

  public function init() {
    add_action('admin_init', Array($this, 'validCf7'));
    add_action('plugins_loaded', Array($this, 'langPlugin'));
    add_action('wp_head', Array($this, 'asyncScript'));
    add_action('wp_footer', Array($this,'cf7EventScript'));
    add_action( 'save_post', Array($this, 'cf7SaveCallback') );
    add_filter( 'wpcf7_form_hidden_fields', Array($this, 'cf7AddHiddenFields'), 10, 1 );
    add_filter( 'wpcf7_editor_panels', Array($this, 'cf7AddTab'), 10, 1 );
    add_filter( 'wpcf7_form_id_attr', Array($this, 'cf7AddIdAttr'), 10, 1 );
    wp_enqueue_style( 'custom_wp_admin_css', plugin_dir_url(__FILE__) . '../css/pimex-tab.css', false, '1.0.0');
  }
}
