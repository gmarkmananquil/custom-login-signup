<?php
/**
 *
 *
 **/

$is_flash_session_set = false;

require_once BASEPATH . DS . "classes" . DS . "AlertMessage.php";

function cls_flash_message($type, $message){
    global $is_flash_session_set;
    $alertMessage = new AlertMessage($type, $message);
    $_SESSION[CUSTOM_LOGIN_SIGNUP_FLASH_SESSION][] = serialize($alertMessage);
    $is_flash_session_set = true;
}

function cls_get_flash_message(){
    return $_SESSION[CUSTOM_LOGIN_SIGNUP_FLASH_SESSION];
}

function cls_redirect_login(){
    $login_page = get_option(CUSTOM_LOGIN_PAGE);
    $location = get_permalink($login_page);
    wp_safe_redirect($location);
    exit;
}

function cls_redirect_signup(){
    $signup_page = get_option(CUSTOM_SIGNUP_PAGE);
    $location = get_permalink($signup_page);
    wp_safe_redirect($location);
    exit;
}

function cls_validate_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function cls_validate_password($password){
    //regex from: https://stackoverflow.com/questions/48345922/reference-password-validation
    //The following regex ensures at least one lowercase, uppercase, number, and symbol exist in a 8+ character length password
    return preg_match("/^(?=\P{Ll}*\p{Ll})(?=\P{Lu}*\p{Lu})(?=\P{N}*\p{N})(?=[\p{L}\p{N}]*[^\p{L}\p{N}])[\s\S]{8,}$/", $password);
}

function cls_validate_name($name){
    return preg_match("/^([a-zA-Z' ]{2,})$/", $name);
}

function cls_show_messages(){
    global $is_flash_session_set;
    $is_flash_session_set = false;
    $flash_messages = cls_get_flash_message();
    ?>
    <?php if(is_array($flash_messages)): ?>
        <?php foreach($flash_messages as $message): ?>
            <?php $message = unserialize($message); ?>
            <div class="alert alert-<?php echo $message->type; ?>">
                <p><?php echo $message->message; ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php
}


//remove session
add_action("wp_footer", function(){
    global $is_flash_session_set;
    if(!$is_flash_session_set){
        $_SESSION[CUSTOM_LOGIN_SIGNUP_FLASH_SESSION] = null;
        unset($_SESSION[CUSTOM_LOGIN_SIGNUP_FLASH_SESSION]);
    }
    $is_flash_session_set = false;
});
