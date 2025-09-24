<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="latepoint-w">
	<div class="os-form-w latepoint-login-form-w" data-success-action="reload">
        <?php
        if(OsAuthHelper::is_customer_auth_enabled()){ ?>
        <form action="" class="latepoint-form">
            <div class="latepoint-customer-otp-input-container"></div>
            <div class="latepoint-customer-auth-options-wrapper hide-when-entering-otp">
                <div class="latepoint-customer-box-title"><?php _e('Sign in to your account', 'latepoint'); ?></div>
                <?php
                echo '<div class="os-step-existing-customer-login-w">';
                    echo OsAuthHelper::auth_form_html( true, new OsCustomerModel() );
                echo '</div>';
                echo '<div class="os-password-reset-form-holder"></div>';
                if ( apply_filters( 'latepoint_customer_login_show_other_options', false ) ) { ?>
                    <div class="os-social-or"><span><?php _e( 'OR', 'latepoint' ); ?></span></div>
                    <?php
                }
                ?>
                <?php do_action('latepoint_after_customer_login_form'); ?>
            </div>
        </form>
        <?php
        }else{
            echo '<div>'.__('Customer authentication is disabled', 'latepoint').'</div>';
        } ?>
	</div>
</div>