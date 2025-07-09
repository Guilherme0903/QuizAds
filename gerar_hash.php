```php
   <?php
   $senha = '123456';
   $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
   echo $senha_hash;
   ?>