<?php
/**
 *
 *
 *
 */

if(!function_exists("custom_signup_shortcode")){

    //if already loggedin , redirect user to homepage when they access signup page
    add_action("wp_head", function(){
        global $post;
        preg_match("[custom-signup]", $post->post_content, $matches);
        if(count($matches)>0){
            update_option(CUSTOM_SIGNUP_PAGE, $post->ID);
            if(is_user_logged_in()){ ?>
                <script>
                    window.location.href = "<?php echo site_url(); ?>";
                </script>
                <?php
            }
        }
    });

    add_shortcode("custom-signup", "custom_signup_shortcode");
    function custom_signup_shortcode(){
        global $post;
        //lol
        $email = $fname = $lname = $pass = $imonth = $iday = $iyear = "";
        
        if(isset($_POST["custom-signup-submit"])){
			$email = sanitize_text_field(trim($_POST["email"]));
			$fname = sanitize_text_field(trim($_POST["first-name"]));
			$lname = sanitize_text_field(trim($_POST["last-name"]));
			//$pass  = sanitize_text_field(trim($_POST["password"]));
	
			$imonth = sanitize_text_field(trim($_POST["month"]));
			$iday   = sanitize_text_field(trim($_POST["day"]));
			$iyear  = sanitize_text_field(trim($_POST["year"]));
        }
        
        
        $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "November", "December");
        $current_year = date("Y");
        $login_page = get_option(CUSTOM_LOGIN_PAGE);
        $login_url = get_permalink($login_page);
        ob_start();
        ?>
        <div id="custom-signup" class="">
            <div class="social-signup">
                <p>Sign up with <a href="#">Facebook</a> or <a href="#">Google</a></p>
            </div>

            <div class="spacer"><span>or</span></div>

            <form class="custom-signup-form" action="<?php echo get_permalink($post->ID); ?>" method="post">
                <?php //wp_nonce_field("custom-signup-form"); ?>
                <div class="form-inner">

                    <?php cls_show_messages(); ?>

                    <div class="form-group">
                        <input type="email" name="email" id="email" placeholder="Email address" value="<?php echo $email; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="first-name" id="first-name" placeholder="First name" value="<?php echo $fname; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="last-name" id="last-name" placeholder="Last name" value="<?php echo $lname; ?>">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" placeholder="Create Password" value="">
                    </div>

                    <div class="birthday-group">
                        <strong>Birthday</strong>
                        <select class="month" name="month">
                            <option value="">Month</option>
                            <?php foreach($months as $key => $month): ?>
                                <option value="<?php echo $key + 1; ?>" <?php echo $imonth == ($key + 1)?"selected='selected'":""; ?>><?php echo $month; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select class="day" name="day">
                            <option value="">Day</option>
                            <?php for($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $iday == $i?"selected='selected'":""; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select class="year" name="year">
                            <option value="">Year</option>
                            <?php for($i = $current_year; $i >= ($current_year - 100); $i-- ): ?>
                                <option value="<?php echo $i; ?>" <?php echo $iyear == $i?"selected='selected'":""; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="terms-and-condition">
                        By clicking Sign up or Continue with, I agree to our Terms of Service, Payments Terms of Service, Privacy Policy, and Nondiscrimination Policy.
                    </div>

                    <div class="submit-button">
                        <input type="submit" name="custom-signup-submit" value="Sign up" />
                    </div>

                    <div class="login-group">
                        <span>Already have an account? </span>
                        <a href="<?php echo $login_url; ?>">Log in</a>
                    </div>

                </div>
            </form>

        </div>

        <?php
        $html = ob_get_contents();
        ob_clean();
        return $html;
    }

}

if(!function_exists("custom_signup_init")){
    //TODO: apply ajax here too.
    add_action("init", "custom_signup_init");
    function custom_signup_init(){
        
        if(isset($_POST["custom-signup-submit"])){

            $email = sanitize_text_field(trim($_POST["email"]));
            $fname = sanitize_text_field(trim($_POST["first-name"]));
            $lname = sanitize_text_field(trim($_POST["last-name"]));
            $pass  = sanitize_text_field(trim($_POST["password"]));

            $month = sanitize_text_field(trim($_POST["month"]));
            $day   = sanitize_text_field(trim($_POST["day"]));
            $year  = sanitize_text_field(trim($_POST["year"]));

            //security here...

            $has_error = false;

            if(!cls_validate_email($email)){
                cls_flash_message(AlertMessage::ERROR, "Email Address is either empty or invalid!", "email");
                $has_error = true;
            }

            if(!cls_validate_name($fname)){
                cls_flash_message(AlertMessage::ERROR, "First name is either empty or invalid!", "first-name");
                $has_error = true;
            }

            if(!cls_validate_name($lname)){
                cls_flash_message(AlertMessage::ERROR, "Last name is either empty or invalid!", "last-name");
                $has_error = true;
            }

            if(!cls_validate_password($pass)){
                cls_flash_message(AlertMessage::ERROR, "Password is either empty or invalid!", "password");
                $has_error = true;
            }

            if($month=="" || $day =="" || $year == ""){
                cls_flash_message(AlertMessage::ERROR, "Birthdate cant be empty!", "birthday");
                $has_error = true;
            }
            
            if($has_error) return ;
            
            $user_id = username_exists($email);
            if(!$user_id && !email_exists($email)){
                $user_id = wp_create_user($email, $pass, $email);
                if($user_id){

                    $user_meta = get_user_meta($user_id);

                    update_user_meta($user_id, "nice_name", $fname . " " . $lname);
                    update_user_meta($user_id, "first_name", $fname);
                    update_user_meta($user_id, "last_name", $lname);
                    add_user_meta($user_id, "birthday", $month . "/" . $day . "/" . $year, true);
                    add_user_meta($user_id, "bday-month", $month, true);
                    add_user_meta($user_id, "bday-day", $day, true);
                    add_user_meta($user_id, "bday-year", $year, true);

                    cls_flash_message(AlertMessage::SUCCESS, "Successfully registered!");
                    cls_redirect_login();
                }else{
                    //Opps! Error creating user
                    cls_flash_message(AlertMessage::ERROR, "Error creating user! Please try again!");
                    cls_redirect_signup();
                }
            }else{
                cls_flash_message(AlertMessage::ERROR, "Email already exists! Please try another email");
                cls_redirect_signup();
            }

        }
    }
}
