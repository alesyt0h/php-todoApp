<?php

class Validations {

    static $message;

    private $strategy;

    public function __construct(singleValidation $validator){
        self::$message = '';
        $this->strategy = $validator;
    }

    public function setValidator(singleValidation $validator){
        $this->strategy = $validator;
    }

    public function performValidation(){
        $this->strategy->validation();
        return self::$message;
    }

}

interface singleValidation {
    public function validation();
}

// Needed on UserController to instantiate Validation class before any procedure
class EmptyRuleSet implements singleValidation {
    public function validation(){
        return;
    }
}

class UserValidation implements singleValidation {

    public function __construct(private string $userName, private bool $isRegister = false){
        $this->userDB = new UserModel();
    }

    public function validation(){
        $allowedChars = '/^[a-zA-Z0-9äöüÄÖÜ_\-.]*$/';
        
        if(strlen($this->userName) < 3){
            return Validations::$message .= 'Username must have at least 3 characters<br>';
        }

        if(strlen($this->userName) > 50){
            return Validations::$message .= 'Usernames can\'t have more than 50 characters<br>';
        }

        if(!preg_match($allowedChars, $this->userName)){
            return Validations::$message .= 'Usernames can only contain letters, numbers, underscores, hyphens and/or dots.<br>';
        }
        
        if($this->isRegister){
            $userExists = $this->userDB->userExists($this->userName);
    
            if($userExists){
                return Validations::$message .= 'This username is taken! Please choose another<br>';
            }
        }

    }

}

class PasswordValidation implements singleValidation {

    public function __construct(
        private string $passToValidate, 
        private string $currentPassword = '',
        private string $newPassword = '', 
        private string $confirmPassword = '',
        private bool $isChangingPassword = false
    ){}

    public function validation(){

        if(strlen($this->passToValidate) < 6){
            return Validations::$message .= 'Password must have at least 6 characters<br>';
        }

        if(strlen($this->passToValidate) > 50){
            return Validations::$message .= 'Passwords can\'t have more than 50 characters<br>';
        }

        if($this->isChangingPassword){

            if(strlen($this->newPassword) < 6){
                return Validations::$message .= 'Your new password must have at least 6 characters<br>';
            }

            if(strlen($this->newPassword) > 50){
                return Validations::$message .= 'Passwords can\'t have more than 50 characters<br>';
            }

            if(!password_verify($this->passToValidate, $this->currentPassword)){
                return Validations::$message .= 'Current password is incorrect<br>';
            }

            if($this->newPassword !== $this->confirmPassword){
                return Validations::$message .= 'New passwords doesn\'t match<br>';
            }

        }
    }
}

class EmailValidation implements singleValidation {

    public function __construct(private string $email){
        $this->userDB = new UserModel();
    }

    public function validation(){
        $emailPattern = '/^[a-z0-9._%+-]+@[a-z0-9.-]{2,}\\.[a-z]{2,4}$/';
        
        if(!preg_match($emailPattern, $this->email)){
            return Validations::$message .= 'Please introduce a valid email<br>';
        }

        if(strlen($this->email) > 50){
            return Validations::$message .= 'Email addresses can\'t have more than 50 characters<br>';
        }

        $mailExists = $this->userDB->mailExists($this->email);

        if($mailExists){
            return Validations::$message .= 'This email is already in use. Use another<br>';
        }
    }
}

class AvatarValidation implements singleValidation {

    public function __construct(private string $avatar){}

    public function validation(){
        $urlPattern = '/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/';
        
        if(strlen($this->avatar) > 255){
            return Validations::$message .= 'Avatar URL is too long<br>';
        }

        if(!preg_match($urlPattern, $this->avatar)){
            return Validations::$message .= 'Avatar URL is not a valid URL!<br>';
        }

        // ! Requires openSSL - extension=php_openssl.dll
        // Note: Not very accurate, some CDN's like CloudFlare, hides the content-type header.
        $result = @getimagesize($this->avatar);
        $result = ($result && strtolower(substr($result['mime'], 0, 5)) == 'image' ? true : false);

        if(extension_loaded('openssl')){
            if(!$result){
                return Validations::$message .= 'The Avatar you entered is not a valid image!<br/> 
                Make sure your URL ends in a known image<br/>
                extension like .jpg, .png, .webp etc..<br>';
            }
        }
    }

}

class TodoValidation implements singleValidation {

    public function __construct(private string $todo){
    }

    public function validation(){

        if(!strlen($this->todo)){
            return Validations::$message .= 'The Todo cannot be empty';
        }

        if(strlen($this->todo) > 255){
            return Validations::$message .= 'Todos can\'t have more than 255 characters<br>';
        }
    }
}

class LoginSuperSet extends Validations {

    public function __construct(private string $userName, private string $pass){

        Validations::$message = '';

        $this->setValidator(new UserValidation($userName));
        $this->performValidation();
    
        $this->setValidator(new PasswordValidation($pass));
        $this->performValidation();
    }
}

class RegisterSuperSet extends Validations {

    public function __construct(private string $userName, private string $pass, private string $email){
    
        Validations::$message = '';

        $this->setValidator(new UserValidation($userName, true));
        $this->performValidation();

        $this->setValidator(new PasswordValidation($pass));
        $this->performValidation();

        $this->setValidator(new EmailValidation($email));
        $this->performValidation();
    }
}



?>