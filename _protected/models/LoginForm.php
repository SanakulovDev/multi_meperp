<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {
  public $username;
  public $email;
  public $password;
  public $rememberMe = true;
  public $status; // holds the information about user status

  /**
   * @var User
   */
  private $_user = false;

  /**
   * Returns the validation rules for attributes.
   * @return array
   */
  public function rules() {
    return [
      ['email', 'email'],
      ['password', 'validatePassword'],
      ['rememberMe', 'boolean'],
      // username and password are required on default scenario
      [['username', 'password'], 'required', 'on' => 'default', 'message' => '{attribute} не может быть пустым'],
      // email and password are required on 'lwe' (login with email) scenario
      [['email', 'password'], 'required', 'on' => 'lwe'],
    ];
  }

  /**
   * Validates the password.
   * This method serves as the inline validation for password.
   * @param string $attribute The attribute currently being validated.
   * @param array  $params    The additional name-value pairs.
   */
  public function validatePassword($attribute, $params) {
    if ($this->hasErrors()) {
      return false;
    }

    $user = $this->getUser();

    if ($user) {
      try {
        if (Yii::$app->ad->auth()->attempt($this->username . '@' . $user->account_suffix, $this->password)) {
          return true;
        } else {
          // if ($user->validatePassword($this->password)) {
          // 	return true;
          // }
          $this->addError('password', Yii::t('app', 'Incorrect username or password.'));
        }
      } catch (Exception $ex) {
        if ($user->validatePassword($this->password)) {
          return true;
        } else {
          $this->addError('password', Yii::t('app', 'LDAP connection fail or Incorrect username or password.'));
        }
      }
    } else {
      $this->addError('username', Yii::t('app', 'This name has not been added to the userlist of this system.'));
    }
  }

  /**
   * Returns the attribute labels.
   * @return array
   */
  public function attributeLabels() {
    return [
      'username' => Yii::t('app', 'Username'),
      'password' => Yii::t('app', 'Password'),
      'email' => Yii::t('app', 'Email'),
      'rememberMe' => Yii::t('app', 'Remember me'),
    ];
  }

  /**
   * Logs in a user using the provided username|email and password.
   * @return bool Whether the user is logged in successfully.
   */
  public function login() {
    // return true;
    if (!$this->validate()) {
      return false;
    }
    $user = $this->getUser();
    if (!$user) {
      return false;
    }
    // vd($user);
    // if there is user but his status is inactive, write that in status property so we know for later
    if ($user->status == User::STATUS_INACTIVE) {
      $this->status = $user->status;
      return false;
    }
    // vd(Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0));
    // return ;
    return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
  }

  /**
   * Helper method responsible for finding user based on the model scenario.
   * In Login With Email 'lwe' scenario we find user by email, otherwise by username
   * @return object The found User object.
   */
  private function findUser() {
    if (!($this->scenario === 'lwe')) {
      return User::findByUsername($this->username);
    }
    return $this->_user = User::findByEmail($this->email);
  }

  /**
   * Method that is returning User object.
   * @return User|null
   */
  public function getUser() {
    if ($this->_user === false) {
      $this->_user = $this->findUser();
    }
    return $this->_user;
  }
}
