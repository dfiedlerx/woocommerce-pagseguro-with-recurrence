<?php


class WC_PagSeguro_Controle_Recorrencia {


    public static function init() {


        //Admin Menu
        add_action('admin_menu', 'WC_PagSeguro_Controle_Recorrencia::register_my_custom_submenu_page');

        add_action( 'rest_api_init', function () {
            register_rest_route( 'admin', 'cancelarAssinatura/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => 'WC_PagSeguro_Controle_Recorrencia::cancelarAssinatura',
            ) );
        } );

        add_action( 'rest_api_init', function () {
            register_rest_route( 'admin', 'suspenderAssinatura/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => 'WC_PagSeguro_Controle_Recorrencia::suspenderAssinatura',
            ) );
        } );

        //User Menu
        add_action( 'init', 'WC_PagSeguro_Controle_Recorrencia::my_account_recorrence_endpoint' );
        add_filter( 'woocommerce_account_menu_items', 'WC_PagSeguro_Controle_Recorrencia::my_account_recorrence_tab', 10, 1 );
        add_action( 'woocommerce_account_pagamentos_recorrentes_endpoint', 'WC_PagSeguro_Controle_Recorrencia::my_account_recorrence_tab_content' );

        add_action( 'woocommerce_order_details_after_order_table', 'WC_PagSeguro_Controle_Recorrencia::nolo_custom_field_display_cust_order_meta', 10, 1 );

    }

    public static function nolo_custom_field_display_cust_order_meta($order){

        if (!empty(get_post_meta( $order->id, 'origin_order', true ))) {

            echo '<p><strong>Este pedido foi gerado através de uma compra recorrente.</p><br>';
            echo '<p><strong>'.__('Pedido Original: ').'</strong> ' . get_post_meta( $order->id, 'origin_order', true ). '</p><br><br><br>';

        }

    }

    public static function my_account_recorrence_tab_content () {

        $assinaturas = get_posts(array(
            'author'        =>  get_current_user_id(),
            'posts_per_page'    => 300,
            'offset'            => 0,
            'category'          => '',
            'category_name'     => '',
            'orderby'           => 'ID',
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            'order'             => 'DESC',
            'post_type'         => 'assinatura'
            /*'meta_key'        => 'partner-submission-status',
            'meta_value'        => 'goedgekeurd',*/
        ));

        ?>
        <h3>Listagem de todos os pagamentos recorrentes</h3>


        <table class="wp-list-table widefat fixed striped posts" style="text-align: center;">
            <thead>
            <th>Nº Compra</th>
            <th>Valor</th>
            <th>Per. Cobrança</th>
            <th>Data de adesão</th>
            <th>Ativa?</th>
            <th>Cancelada?</th>
            <th>Ações</th>
            </thead>
            <tbody>

            <?php
            foreach ($assinaturas as $currentAssinatura)
            {
                ?>
                <tr class="iedit author-self level-0  post-password-required hentry">
                    <td>
                        <a href="<?php echo get_site_url() . '/minha-conta/view-order/' . get_post_meta($currentAssinatura->ID, 'compra', true); ?>">
                            <?php echo '#' . get_post_meta($currentAssinatura->ID, 'compra', true) ?>
                        </a>
                    </td>
                    <td><?php echo !empty(get_post_meta($currentAssinatura->ID, 'valor', true)) ? 'R$ ' . number_format(get_post_meta($currentAssinatura->ID, 'valor', true), 2, ',', '.' ) : ''; ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'periodo', true) ?></td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($currentAssinatura->post_date . ' -3 hours')); ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'active', true) == 1 ? 'Sim' : 'Não' ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'cancelled', true) == 1 ? 'Sim' : 'Não' ?></td>
                    <td style="text-align: center">
                        <?php
                        if (get_post_meta($currentAssinatura->ID, 'cancelled', true) == 0) {
                            ?>
                            <div style="margin-bottom: 24px;">
                                <a class="woocommerce-button button" href="<?php echo get_site_url() . '/wp-json/admin/suspenderAssinatura/' . $currentAssinatura->ID ?>">

                                    <?php echo (get_post_meta($currentAssinatura->ID, 'active', true) == 1 ? 'Suspender' : 'Ativar'); ?>

                                </a>
                            </div>
                            <div>
                                <a class="woocommerce-button button" href="<?php echo get_site_url() . '/wp-json/admin/cancelarAssinatura/' . $currentAssinatura->ID ?>">
                                    Cancelar
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>

        <?php

    }

    public static function my_account_recorrence_tab ( $items ) {

        $items = array_slice($items, 0, 2, true) +
            array("pagamentos_recorrentes" => __('Pagamentos Recorrentes', 'iconic')) +
            array_slice($items, 2, count($items) - 1, true) ;

        return $items;

    }

    public static function my_account_recorrence_endpoint () {

        add_rewrite_endpoint( 'pagamentos_recorrentes', EP_PAGES );

    }

    public static function register_my_custom_submenu_page() {

        add_submenu_page
        (
            'woocommerce',
            'Pagamentos Recorrentes',
            'Pagamentos Recorrentes',
            'manage_options',
            'woocommerce-recorrence-manager',
            'WC_PagSeguro_Controle_Recorrencia::pagamento_recorrente_admin_menu'
        );


    }

    public static function pagamento_recorrente_admin_menu() {

        $assinaturas = get_posts(array(
            'posts_per_page'    => 300,
            'offset'            => 0,
            'category'          => '',
            'category_name'     => '',
            'orderby'           => 'ID',
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            'order'             => 'DESC',
            'post_type'         => 'assinatura'
            /*'meta_key'        => 'partner-submission-status',
            'meta_value'        => 'goedgekeurd',*/
        ));

        ?>
        <h3>Listagem de todos os pagamentos recorrentes</h3>


        <table class="wp-list-table widefat fixed striped posts" style="text-align: center;">
            <thead>
            <th>Cód. Assinatura</th>
            <th>Cliente</th>
            <th>Nº Compra</th>
            <th>Valor</th>
            <th>Per. Cobrança</th>
            <th>Data de adesão</th>
            <th>Ativa?</th>
            <th>Cancelada?</th>
            <th></th>
            </thead>
            <tbody>

            <?php
            foreach ($assinaturas as $currentAssinatura)
            {
                ?>
                <tr class="iedit author-self level-0  post-password-required hentry">
                    <td><?php echo $currentAssinatura->post_title; ?></td>
                    <td><?php echo get_author_name($currentAssinatura->post_author); ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'compra', true) ?></td>
                    <td><?php echo !empty(get_post_meta($currentAssinatura->ID, 'valor', true)) ? 'R$ ' . number_format(get_post_meta($currentAssinatura->ID, 'valor', true), 2, ',', '.' ) : ''; ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'periodo', true) ?></td>
                    <td><?php echo date('d-m-Y H:i:s', strtotime($currentAssinatura->post_date . ' -3 hours')); ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'active', true) == 1 ? 'Sim' : 'Não' ?></td>
                    <td><?php echo get_post_meta($currentAssinatura->ID, 'cancelled', true) == 1 ? 'Sim' : 'Não' ?></td>
                    <td style="text-align: center">
                        <?php
                        if (get_post_meta($currentAssinatura->ID, 'cancelled', true) == 0) {
                            ?>
                            <div style="margin-bottom: 8px;">
                                <a href="<?php echo get_site_url() . '/wp-json/admin/suspenderAssinatura/' . $currentAssinatura->ID ?>">
                                    <button class="button">
                                        <?php echo (get_post_meta($currentAssinatura->ID, 'active', true) == 1 ? 'Suspender' : 'Ativar'); ?>
                                    </button>
                                </a>
                            </div>
                            <div>
                                <a href="<?php echo get_site_url() . '/wp-json/admin/cancelarAssinatura/' . $currentAssinatura->ID ?>">
                                    <button class="button">
                                        Cancelar
                                    </button>
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>

        <?php
    }




    public static function suspenderAssinatura (WP_REST_Request $request) {

        header("Content-Type: text/html; charset=ISO-8859-1",true);

        $pagseguroApi = new WC_PagSeguro_API();

        $pagseguroApi->suspendeAtivaAssinatura($request->get_param('id'));

        echo
            "
        <script type='text/javascript'> 
            history.back();
            history.go(-1);
        </script>";

    }

    public static function cancelarAssinatura (WP_REST_Request $request) {

        header("Content-Type: text/html; charset=ISO-8859-1",true);

        $pagseguroApi = new WC_PagSeguro_API();

        $pagseguroApi->cancelaAssinatura($request->get_param('id'));

        echo
            "
        <script type='text/javascript'> 
            history.back();
            history.go(-1);
        </script>";
    }

}
