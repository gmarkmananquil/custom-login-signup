<?php
/**
 *
 *
 *
 */

if(!function_exists("custom_login_shortcode")){

    add_action("wp_head", function(){
        global $post;
        preg_match("[custom-login]", $post->post_content, $matches);
        if(count($matches)>0){
            update_option(CUSTOM_LOGIN_PAGE, $post->ID);
            if(is_user_logged_in()){ ?>
                <script>
                    window.location.href = "<?php echo site_url(); ?>";
                </script>
                <?php
            }
        }
    });

    add_shortcode("custom-login", "custom_login_shortcode");
    function custom_login_shortcode($atts){
        global $post;
        $signup_page = get_option(CUSTOM_SIGNUP_PAGE);
        $signup_url = get_permalink($signup_page);
        ob_start();
        ?>
        <div id="custom-login">
            <div class="social-login">
                <form class="custom-login-social-form" action="<?php echo get_permalink($post->ID); ?>" method="post">
                    <div class="facebook-login">
                        <button type="submit" name="submit-custom-facebook-login">Log in with Facebook</button>
                    </div>
                    <div class="google-login">
                        <button type="submit" name="submit-custom-google-login">Log in with Google</button>
                    </div>
                </form>
            </div>

            <div class="spacer">
                <span>or</span>
            </div>

            <form class="form" action="<?php echo get_permalink($post->ID); ?>" method="post">

                <?php cls_show_messages(); ?>

                <div class="form-group">
                    <input type="email" name="email" class="email" placeholder="Email Address" />
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="password" value="" placeholder="Password">
                </div>

                <div class="remember-me-group" >
                    <input type="checkbox" name="remember-me" value="1" id="remember">
                    <label for="remember">Remember me</label>
                </div>


                <input type="submit" name="custom-login-submit" class="custom-login-submit" value="Log in">

                <div class="signup-group">
                    <span>Don't have an account? </span>
                    <a href="<?php echo $signup_url; ?>">Sign up</a>
                </div>

            </form>
        </div>
        <?php
        return ob_get_clean();
    }

}

if(!function_exists("custom_login_init")){
    //TODO: apply ajax here too.
    add_action("init", "custom_login_init");
    function custom_login_init(){

        //TODO: Improve security, limit user loggedin try by 3 times

        if(isset($_POST["custom-login-submit"])){
            $wp_hasher = new PasswordHash(8, TRUE);

            $email          = sanitize_text_field(trim($_POST["email"]));
            $pass           = sanitize_text_field(trim($_POST["password"]));
            $remember_me    = sanitize_text_field(trim($_POST["remember-me"]));

            $user = get_user_by("email", $email);
            if($user){
                $tpass = wp_hash_password($pass);
                if($wp_hasher->CheckPassword($pass, $user->data->user_pass)){
                    //loggedin
                    $rem = ($remember_me==1)?true:false;
                    wp_set_auth_cookie($user->data->ID, $rem);
                    //redirect to home
                    //TODO: we can have a welcome message for login user before redirecting to home page
                    cls_redirect_login();
                }else{
                    cls_flash_message(AlertMessage::ERROR, "Invalid email and password, please try again!");
                    //cls_redirect_login();
                }
            }else{
                cls_flash_message(AlertMessage::ERROR, "Email does not exist, please sign up or try another email");
                //cls_redirect_login();
            }
        }
    }
}
