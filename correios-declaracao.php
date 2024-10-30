<?php
/**
 * Plugin Name: Correios Etiqueta e Declaração
 * Plugin URI: https://www.alcmidia.com.br/plugin-correios-etiqueta-e-declaracao/
 * Description: Gera etiqueta para envio pelos correios e declaração de conteúdo.
 * Version: 1.18
 * Author: pileggi
 * Author URI: http://www.alcmidia.com.br
 * Tested up to: 6.6
 * Stable tag: 1.0
 */

 
//require_once plugin_dir_path(__FILE__) . 'includes/menu.php';



// create config page
add_action( 'admin_init', 'correios_options_init' );
add_action( 'admin_menu', 'correios_options_page' );

function correios_options_init(){
    register_setting(
        'correios_options_group',
        'correios_options',
        'correios_options_validate'
    );
}
// end confg page

// insert options
function correios_options_validate( $input ) {
    // do some validation here if necessary
    return $input;
}

// create menu
function correios_options_page() {
    add_menu_page(
        'Etiqueta Correios',
        'Etiqueta Correios',
        'manage_options',
        'correios_options',
        'correios_render_options'
    );
//    add_submenu_page('correios_options','Dados do Remetente','Dados do Rementente ','manage_options','ad_insert','ad_insert_fields');
//    add_submenu_page('correios_options','Ads Report','Ads Report','manage_options','ads_report','ads_report_page');
}
//end menu

// setting link on plugins page
add_filter( 'plugin_action_links_correios-declaracao/correios-declaracao.php', 'nc_settings_link' );
function nc_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'correios_options',
		get_admin_url() . 'admin.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>". __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}//end nc_settings_link()

// functions
function data_link($order) {
  
  // Metabox content
  	$ordersin = new WC_Order( $order );
    $status = $ordersin->get_status();
    $dessob=$ordersin->get_shipping_last_name();
    $despri=$ordersin->get_shipping_first_name();
    $desnom=urlencode($despri.' '.$dessob);
    $desmai=urlencode($ordersin->get_billing_email());
    $desfon=urlencode($ordersin->get_billing_phone());
    $desnum=$ordersin->get_meta('_shipping_number');
    $desend=urlencode($ordersin->get_shipping_address_1().', '.$desnum);
    $descom=urlencode($ordersin->get_shipping_address_2());
    $desbai=$ordersin->get_meta('_shipping_neighborhood');
    $pedcod=$ordersin->get_id();

    //pega frete para fazer comparativo
    //$frete =$ordersin->order_shipping;
    //echo 'teste:'.$frete;


    $descid=urlencode($ordersin->get_shipping_city());
    $desest=urlencode($ordersin->get_shipping_state());
    $descep=urlencode($ordersin->get_shipping_postcode());
    $pedtotal = urlencode($ordersin->get_subtotal());
    
    $options = get_option( 'correios_options' );
    $remnom=urlencode($options['remnom']);
    $remend=urlencode($options['remend']);
    $remnro=urlencode($options['remnum']);
    $remcom=urlencode($options['remcom']);
    $rembai=urlencode($options['rembai']);
    $remcid=urlencode($options['remcid']);
    $remest=urlencode($options['remest']);
    $remcep=urlencode($options['remcep']);
    $remcpf=urlencode($options['remcpf']);
    $remusr=urlencode($options['remusr']);
    $remcar=urlencode($options['remcar']);
    $remapi=urlencode($options['remapi']);
    $remmai=get_option('admin_email');

 
    $url = get_home_url();
   
    $i=0;
    $produtos_url='';
    // Get and Loop Over Order Items
    foreach ( $ordersin->get_items() as $item_id => $item ) {
      
      $product_name = urlencode($item->get_name());
      $quantity = urlencode($item->get_quantity());
      $total = urlencode($item->get_total());
      $i++;
      $produtos_url = $produtos_url.'&pronom'.$i.'='.$product_name.'&proqtd'.$i.'='.$quantity.'&propre'.$i.'='.$total.'&pedtot='.$pedtotal;
    }

    if ($status=='processing') {
    
      // Prepare the button data on orders page
      $url    = esc_url('https://www.alcmidia.com.br/aplicativos/correios/redireciona.php?pedcod='.$pedcod.'&desend='.$desend.'&descom='.$descom.'&desbai='.$desbai.'&descid='.$descid.'&desest='.$desest.'&descep='.$descep.'&desnom='.$desnom.'&remend='.$remend.'&desmai='.$desmai.'&desfon='.$desfon.'&remnro='.$remnro.'&remcom='.$remcom.'&rembai='.$rembai.'&remcid='.$remcid.'&remest='.$remest.'&remcep='.$remcep.'&remnom='.$remnom.'&remcpf='.$remcpf.$produtos_url.'&url='.$url.'&email='.$remmai.'&remusr='.$remusr.'&remcar='.$remcar.'&remapi='.$remapi);
      $name   = esc_attr( __('Etiqueta dos Correios', 'woocommerce' ) );
      $class  = esc_attr( 'tracking' );

      printf( '<a class="button wc-action-button wc-action-button-%s %s" href="%s" title="%s" target="_blank">%s</a>', $class, $class, $url, $name, $name );
      
    }  
  
}

// config page form
function correios_render_options() {
    ?>
    <div class="wrap">
        <form method="post" action="options.php">
            <?php
            settings_fields( 'correios_options_group' );
            $options = get_option( 'correios_options' );
            ?>
                <h1 style="padding: 20px 0px 10px 0px;">Dados do Remetente</h1>
                <div style="margin: 10px 0px 10px 0px;">Insira abaixo os dados do local de envio:</div>
				<? if($_GET["settings-updated"]) {echo '<span style="display: inline-block; background-color: #99ff99; padding: 10px; margin: 10px 10px 20px 0px;">Dados Atualizados com Sucesso!</span>'; }  ?>
                <div style="float: left; width: 100%; margin-bottom: 10px;">
                  <?php
                    $remnom=$options['remnom'];
                    $remend=$options['remend'];
                    $remnum=$options['remnum'];
                    $remcom=$options['remcom'];
                    $rembai=$options['rembai'];
                    $remcid=$options['remcid'];
                    $remest=$options['remest'];
                    $remcep=$options['remcep'];
                    $remcpf=$options['remcpf'];
                    $remusr=$options['remusr'];
                    $remapi=$options['remapi'];
                    $remcar=$options['remcar'];
                    echo '
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                    <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                      Nome: *<br><input style="width: 600px;" required name="correios_options[remnom]" type="text" value="'.$remnom.'">
                    </div>
                  </div>
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                  Endereço: *<br><input style="width: 600px;" required name="correios_options[remend]" type="text" value="'.$remend.'"> 
                    </div>
                  </div>
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 0px 10px 20px;">
                      Número: *<br><input style="width: 160px;" required name="correios_options[remnum]" type="text" value="'.$remnum.'"> 
                    </div>
                    <div style="display: table-cell; padding: 5px 20px 10px 20px; margin-left:2px;">
                    Complemento: <br><input style="width: 418px;" name="correios_options[remcom]" type="text" value="'.$remcom.'"> 
                    </div>
                  </div>
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                      Bairro: * <br><input style="width: 600px;" name="correios_options[rembai]" type="text" value="'.$rembai.'"> 
                    </div>
                  </div>
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 0px 10px 20px;">
                      Cidade: *<br><input required style="width: 478px;" name="correios_options[remcid]" type="text" value="'.$remcid.'"> 
                    </div>
                    <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                    Estado: *<br><input required style="width: 100px;" name="correios_options[remest]" type="text" value="'.$remest.'"> 
                    </div>
                  </div>
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 0px 10px 20px;">
                      CEP: *<br><input style="width: 290px;" required name="correios_options[remcep]" type="text" value="'.$remcep.'"> 
                    </div>
                    <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                    CPF/CNPJ: <br><input style="width: 288px;" name="correios_options[remcpf]" type="text" value="'.$remcpf.'"> 
                    </div>
                  </div>
                  <div>
                  <div style="display: table-cell; padding: 5px 20px 10px 20px; width: 602px;">
                    Abaixo estão os campos para integração com os correios (PLP) para assinantes da versão pró, caso não tenha contrato com os correios ou não seja assinante, deixe esses campos em branco para utilizar o plugin gratuitamente.
                    </div>              
                  </div>
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 0px 10px 20px;">
                      Usuário:<br><input style="width: 290px;" name="correios_options[remusr]" type="text" value="'.$remusr.'"> 
                    </div>
                    <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                    Cartão: <br><input style="width: 288px;" name="correios_options[remcar]" type="text" value="'.$remcar.'"> 
                    </div>
                  </div>   
                  <div style="background-color: #fff; width: max-content; margin-bottom:2px;">
                  <div style="display: table-cell; padding: 5px 20px 10px 20px;">
                      API: <br><input style="width: 600px;" name="correios_options[remapi]" type="text" value="'.$remapi.'"> 
                    </div>
                  </div>                                 
                    ';
                  ?>
                </div>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
    <?php   
}


// Add your custom order action button
add_action( 'woocommerce_admin_order_actions_end', 'add_custom_order_actions_button', 100, 1 );
function add_custom_order_actions_button( $order ) {

    // Get the tracking number
    //$traking_number = get_post_meta( $order->get_id(), '_aftership_tracking_number', true );
    //if( empty($traking_number) ) return;

    data_link($order);
}

// button in single order page
function op_register_menu_meta_box()
{
  add_meta_box(
      'box-etiqueta-correios',
      esc_html__( 'Etiqueta Correios', 'text-domain' ),
      'render_meta_box',
      'shop_order', // shop_order is the post type of the admin order page
      'side', // change to 'side' to move box to side column 
      'low' // priority (where on page to put the box)
  );
}
add_action( 'add_meta_boxes', 'op_register_menu_meta_box' );

function render_meta_box($order)
{
  data_link($order);
}


// The icon of your action button (CSS)
add_action( 'admin_head', 'add_custom_order_actions_button_css' );
function add_custom_order_actions_button_css() {
    echo '<style>.wc-action-button-tracking::after { margin-left: 5px; font-family: woocommerce !important; content: "\e01a" !important; }</style>';
}




?>