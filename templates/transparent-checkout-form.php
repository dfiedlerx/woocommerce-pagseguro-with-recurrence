<?php
/**
 * Transparent checkout form.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_PagSeguro/Templates
 * @version 2.12.5
 */

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use CWG\PagSeguro\PagSeguroAssinaturas;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<fieldset id="pagseguro-payment-form" class="<?php echo 'storefront' === basename( get_template_directory() ) ? 'woocommerce-pagseguro-form-storefront' : ''; ?>" data-cart_total="<?php echo esc_attr( number_format( $cart_total, 2, '.', '' ) ); ?>">

	<ul id="pagseguro-payment-methods">
		<?php if ( 'yes' == $tc_credit ) : ?>
		<li><label><input id="pagseguro-payment-method-credit-card" type="radio" name="pagseguro_payment_method" value="credit-card" <?php checked( true, ( 'yes' == $tc_credit ), true ); ?> /> <?php _e( 'Credit Card', 'woocommerce-pagseguro' ); ?></label></li>
		<?php endif; ?>

		<?php if ( 'yes' == $tc_transfer ) : ?>
		<li><label><input id="pagseguro-payment-method-bank-transfer" type="radio" name="pagseguro_payment_method" value="bank-transfer" <?php checked( true, ( 'no' == $tc_credit && 'yes' == $tc_transfer ), true ); ?> /> <?php _e( 'Bank Transfer', 'woocommerce-pagseguro' ); ?></label></li>
		<?php endif; ?>

		<?php if ( 'yes' == $tc_ticket ) : ?>
		<li><label><input id="pagseguro-payment-method-banking-ticket" type="radio" name="pagseguro_payment_method" value="banking-ticket" <?php checked( true, ( 'no' == $tc_credit && 'no' == $tc_transfer && 'yes' == $tc_ticket ), true ); ?> /> <?php _e( 'Banking Ticket', 'woocommerce-pagseguro' ); ?></label></li>
		<?php endif; ?>

        <?php if ( 'yes' == 'yes' ) : ?>
            <li><label><input id="pagseguro-payment-method-recorrence" type="radio" name="pagseguro_payment_method" value="recorrence" <?php checked( true, ( 'yes' == $tc_credit && 'no' == $tc_transfer && 'yes' == $tc_ticket ), true ); ?> /> <?php _e( 'Pagamento Recorrente', 'woocommerce-pagseguro' ); ?></label></li>
        <?php endif; ?>

	</ul>
	<div class="clear"></div>

	<?php if ( 'yes' == $tc_credit ) : ?>
		<div id="pagseguro-credit-card-form" class="pagseguro-method-form">
			<p id="pagseguro-card-holder-name-field" class="form-row form-row-first">
				<label for="pagseguro-card-holder-name"><?php _e( 'Card Holder Name', 'woocommerce-pagseguro' ); ?> <small>(<?php _e( 'as recorded on the card', 'woocommerce-pagseguro' ); ?>)</small> <span class="required">*</span></label>
				<input id="pagseguro-card-holder-name" name="pagseguro_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<p id="pagseguro-card-number-field" class="form-row form-row-last">
				<label for="pagseguro-card-number"><?php _e( 'Card Number', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<input id="pagseguro-card-number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<div class="clear"></div>
			<p id="pagseguro-card-expiry-field" class="form-row form-row-first">
				<label for="pagseguro-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<input id="pagseguro-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<p id="pagseguro-card-cvc-field" class="form-row form-row-last">
				<label for="pagseguro-card-cvc"><?php _e( 'Security Code', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<input id="pagseguro-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="<?php _e( 'CVC', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<div class="clear"></div>
			<p id="pagseguro-card-installments-field" class="form-row form-row-first">
				<label for="pagseguro-card-installments"><?php _e( 'Installments', 'woocommerce-pagseguro' ); ?> <small>(<?php _e( 'the minimum value of the installment is R$ 5,00', 'woocommerce-pagseguro' ); ?>)</small> <span class="required">*</span></label>
				<select id="pagseguro-card-installments" name="pagseguro_card_installments" style="font-size: 1.5em; padding: 4px; width: 100%;" disabled="disabled">
					<option value="0">--</option>
				</select>
			</p>
			<p id="pagseguro-card-holder-cpf-field" class="form-row form-row-last">
				<label for="pagseguro-card-holder-cpf"><?php _e( 'Card Holder CPF', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<input id="pagseguro-card-holder-cpf" name="pagseguro_card_holder_cpf" class="input-text wecfb-cpf-field" type="tel" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<div class="clear"></div>
			<p id="pagseguro-card-holder-birth-date-field" class="form-row form-row-first">
				<label for="pagseguro-card-holder-birth-date"><?php _e( 'Card Holder Birth Date', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<input id="pagseguro-card-holder-birth-date" name="pagseguro_card_holder_birth_date" class="input-text" type="tel" autocomplete="off" placeholder="<?php _e( 'DD / MM / YYYY', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<p id="pagseguro-card-holder-phone-field" class="form-row form-row-last">
				<label for="pagseguro-card-holder-phone"><?php _e( 'Card Holder Phone', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
				<input id="pagseguro-card-holder-phone" name="pagseguro_card_holder_phone" class="input-text" type="tel" autocomplete="off" placeholder="<?php _e( '(xx) xxxx-xxxx', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
			</p>
			<div class="clear"></div>
		</div>
	<?php endif; ?>

	<?php if ( 'yes' == $tc_transfer ) : ?>
		<div id="pagseguro-bank-transfer-form" class="pagseguro-method-form">
			<p><?php _e( 'Select your bank:', 'woocommerce-pagseguro' ); ?></p>
			<ul>
				<li><label><input type="radio" name="pagseguro_bank_transfer" value="bancodobrasil" /><i id="pagseguro-icon-bancodobrasil"></i><span><?php _e( 'Banco do Brasil', 'woocommerce-pagseguro' ); ?></span></label></li>
				<li><label><input type="radio" name="pagseguro_bank_transfer" value="bradesco" /><i id="pagseguro-icon-bradesco"></i><span><?php _e( 'Banco Bradesco', 'woocommerce-pagseguro' ); ?></span></label></li>
			</ul>
			<p><?php _e( '* After clicking "Proceed to payment" you will have access to the link that will take you to your bank\'s website, so you can make the payment in total security.', 'woocommerce-pagseguro' ); ?></p>
			<div class="clear"></div>
		</div>
	<?php endif; ?>

	<?php if ( 'yes' == $tc_ticket ) : ?>
		<div id="pagseguro-banking-ticket-form" class="pagseguro-method-form">
			<p>
				<i id="pagseguro-icon-ticket"></i>
				<?php _e( 'The order will be confirmed only after the payment approval.', 'woocommerce-pagseguro' ); ?>
				<?php if ( 'yes' === $tc_ticket_message ) : ?>
					<br />
					<strong><?php _e( 'Tax', 'woocommerce-pagseguro' ); ?>:</strong> <?php _e( 'R$ 1,00 (rate applied to cover management risk costs of the payment method).', 'woocommerce-pagseguro' ); ?>
				<?php endif; ?>
			</p>
			<p><?php _e( '* After clicking "Proceed to payment" you will have access to banking ticket which you can print and pay in your internet banking or in a lottery retailer.', 'woocommerce-pagseguro' ); ?></p>
			<div class="clear"></div>
		</div>
	<?php endif; ?>

    <div id="pagseguro-recorrence-form" class="pagseguro-method-form">

        <p>* O pagamento será automaticamente feito no cartão e prazos informados. É possível cancelar a qualquer momento caso necessário.</p>

        <p id="pagseguro-card-installments-recorrence-field" class="form-row form-row-first">
            <label for="pagseguro-card-recorrence-time-recorrence">Prazo de recompra <small>(Prazo em que a cobrança será refeita e você receberá novamente A compra)</small> <span class="required">*</span></label>
            <select id="pagseguro-card-recorrence-time-recorrence" name="pagseguro_card_recorrence-time" style="font-size: 1.5em; padding: 4px; width: 100%;">
                <option value="mensal" selected>Mensal</option>
                <option value="trimestral">Bimestral</option>
                <option value="mensal">Trimestral</option>
            </select>
        </p>
        <p id="pagseguro-card-holder-name-recorrence-field" class="form-row form-row-first">
            <label for="pagseguro-card-holder-name-recorrence"><?php _e( 'Card Holder Name', 'woocommerce-pagseguro' ); ?> <small>(<?php _e( 'as recorded on the card', 'woocommerce-pagseguro' ); ?>)</small> <span class="required">*</span></label>
            <input id="pagseguro-card-holder-name-recorrence" name="pagseguro_card_holder_name_recorrence" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <p id="pagseguro-card-number-recorrence-field" class="form-row form-row-last">
            <label for="pagseguro-card-number-recorrence"><?php _e( 'Card Number', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
            <input id="pagseguro-card-number-recorrence" class="input-text wc-credit-card-form-card-number  card-number-recorrence" type="tel" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <div class="clear"></div>
        <p id="pagseguro-card-expiry-recorrence-field" class="form-row form-row-first">
            <label for="pagseguro-card-expiry-recorrence"><?php _e( 'Expiry (MM/YYYY)', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
            <input id="pagseguro-card-expiry-recorrence" class="input-text wc-credit-card-form-card-expiry card-expiry-recorrence" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <p id="pagseguro-card-cvc-recorrence-field" class="form-row form-row-last">
            <label for="pagseguro-card-cvc-recorrence"><?php _e( 'Security Code', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
            <input id="pagseguro-card-cvc-recorrence" class="input-text wc-credit-card-form-card-cvc card-cvc-recorrence" type="tel" autocomplete="off" placeholder="<?php _e( 'CVC', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <div class="clear"></div>
        <p id="pagseguro-card-holder-cpf-recorrence-field" class="form-row form-row-last">
            <label for="pagseguro-card-holder-cpf-recorrence"><?php _e( 'Card Holder CPF', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
            <input id="pagseguro-card-holder-cpf-recorrence" name="pagseguro_card_holder_cpf_recorrence" class="input-text wecfb-cpf-field" type="tel" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <div class="clear"></div>
        <p id="pagseguro-card-holder-birth-date-recorrence-field" class="form-row form-row-first">
            <label for="pagseguro-card-holder-birth-date-recorrence"><?php _e( 'Card Holder Birth Date', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
            <input id="pagseguro-card-holder-birth-date-recorrence" name="pagseguro_card_holder_birth_date_recorrence" class="input-text" type="tel" autocomplete="off" placeholder="<?php _e( 'DD / MM / YYYY', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>
        <p id="pagseguro-card-holder-phone-recorrence-field" class="form-row form-row-last">
            <label for="pagseguro-card-holder-phone-recorrence"><?php _e( 'Card Holder Phone', 'woocommerce-pagseguro' ); ?> <span class="required">*</span></label>
            <input id="pagseguro-card-holder-phone-recorrence" name="pagseguro_card_holder_phone_recorrence" class="input-text" type="tel" autocomplete="off" placeholder="<?php _e( '(xx) xxxx-xxxx', 'woocommerce-pagseguro' ); ?>" style="font-size: 1.5em; padding: 8px;" />
        </p>

        <p id="pagseguro-card-holder-phone-field" class="form-row form-row-last"  style="display: none">
            <label for="pagseguro_cliente_hash"></label>
            <input id="pagseguro_cliente_hash" name="pagseguro_cliente_hash" />
        </p>

        <p id="pagseguro_cartao_token-field" class="form-row form-row-last" style="display: none">
            <label for="pagseguro_cartao_token"></label>
            <input id="pagseguro_cartao_token" name="pagseguro_cartao_token" />
        </p>

        <p id="pagseguro_cartao_bandeira-field" class="form-row form-row-last" style="display: none">
            <label for="pagseguro_cartao_bandeira"></label>
            <input id="pagseguro_cartao_bandeira" name="pagseguro_cartao_bandeira" />
        </p>

        <p id="pagseguro_cartao_bandeira-field" class="form-row form-row-last" style="display: none">
            <label for="pagseguro_cartao_mes"></label>
            <input id="pagseguro_cartao_mes" name="pagseguro_cartao_mes" />
        </p>

        <p id="pagseguro_cartao_bandeira-field" class="form-row form-row-last" style="display: none">
            <label for="pagseguro_cartao_ano"></label>
            <input id="pagseguro_cartao_ano" name="pagseguro_cartao_ano" />
        </p>

        <p id="pagseguro_cartao_bandeira-field" class="form-row form-row-last" style="display: none">
            <label for="pagseguro_cartao_numero"></label>
            <input id="pagseguro_cartao_numero" name="pagseguro_cartao_numero" />
        </p>


        <p id="pagseguro_cartao_bandeira-field" class="form-row form-row-last" style="display: none">
            <label for="pagseguro_cartao_cvv"></label>
            <input id="pagseguro_cartao_cvv" name="pagseguro_cartao_cvv" />
        </p>


        <div class="clear"></div>
    </div>

	<p><?php esc_html_e( 'This purchase is being made in Brazil', 'woocommerce-pagseguro' ); ?> <img src="<?php echo esc_url( $flag ); ?>" alt="<?php esc_attr_e( 'Brazilian flag', 'woocommerce-pagseguro' ); ?>" style="display: inline; float: none; vertical-align: middle; border: none;" /></p>

    <?php
    $pagSeguroSettings = get_option('woocommerce_pagseguro_settings');

    //Fornece as credenciais da api observando se o plugin está no modo sandbox ou não
    if ('yes' == $pagSeguroSettings['sandbox']) {

        $accessInfo = [
            'email' => $pagSeguroSettings['sandbox_email'],
            'token' => $pagSeguroSettings['sandbox_token']
        ];

        $sandBox = true;

    } else {

        $accessInfo = [
            'email' => $pagSeguroSettings['email'],
            'token' => $pagSeguroSettings['token']
        ];

        $sandBox = false;

    }


    $pagseguro = new PagSeguroAssinaturas($accessInfo['email'], $accessInfo['token'], $sandBox);

    $js = $pagseguro->preparaCheckoutTransparente(true);
    echo $js['completo'];
    ?>

    <script>

        window.released = false;

        $('.card-number-recorrence,.card-expiry-recorrence,.card-cvc-recorrence').on('change', function () {

            if (
                '' !== $('.card-number-recorrence').val() &&
                '' !== $('.card-expiry-recorrence').val() &&
                '' !== $('.card-cvc-recorrence').val()
            )
            {

                window.released = true;

                let expirationCase = $('.card-expiry-recorrence').val().split('/');

                $('#pagseguro_cartao_mes').val(expirationCase['0'].replace(/ /g, '') + '');
                $('#pagseguro_cartao_ano').val(expirationCase['1'].replace(/ /g, '') + '');
                $('#pagseguro_cartao_cvv').val($('.card-cvc-recorrence').val().replace(/ /g, '') + '');
                $('#pagseguro_cartao_numero').val($('.card-number-recorrence').val().replace(/ /g, '') + '');

                PagSeguroBuscaHashCliente(); //Cria o Hash identificador do Cliente usado na transição
                PagSeguroBuscaBandeira();   //Através do pagseguro_cartao_numero do cartão busca a bandeira
                PagSeguroBuscaToken();

            }

        });

        var stops = false;
        $('body').mousemove(function () {

            if (

                (
                    !stops &&
                    (
                        '' !== $('.card-number-recorrence').val() &&
                        '' !== $('.card-expiry-recorrence').val() &&
                        '' !== $('.card-cvc-recorrence').val()
                    ) &&
                    (
                        $('#pagseguro_cliente_hash').val() == '' &&
                        $('#pagseguro_cartao_token').val() == '' &&
                        $('#pagseguro_cartao_bandeira').val() == ''
                    )
                )
            ) {

                PagSeguroBuscaHashCliente(); //Cria o Hash identificador do Cliente usado na transição
                PagSeguroBuscaBandeira();   //Através do pagseguro_cartao_numero do cartão busca a bandeira
                PagSeguroBuscaToken();
                stops = true;

            }

        });

    </script>

</fieldset>