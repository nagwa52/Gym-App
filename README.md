# Gym App Using Laravel
A PHP Laravel web application that uses most of Laravel technologies to build that gym system.
The System is based on rules. Admin, City Manager, Gym Manager. 
All Crud operations running using data tables.
and Apis like(register, login, get the remaining training sessions,attendance history of user ,post requests for attending sessions for user)
make verifications by emain and greet the user if he make login , schedule command that runs daily that
will send an email notification to users who didn’t log in from the
past month
## Table of contents

- [Overview](#overview)
    - [Screenshot](#screenshot)
    - [Live](#Deployed version)
- [My process](#my-process)
    - [Built with](#built-with)
    - [Libraries](#Libraries)
- [Author](#authors)


## My process
1) Clone the project

   ``` git clone https://github.com/nagwa52/Gym-App.git```


2) install [composer](https://getcomposer.org/)
3) add your .env file 
6) in the project directory run the following
    ```
    $composer update ; composer install ; composer dump-autoload
    $php artisan migrate 
    $php artisan serve
    $php artisan cache:clear ; php artisan route:clear ; php artisan view:clear ; php artisan config:cache
    ```
<p align="right">(<a href="#top">back to top</a>)</p>

### Built with

* [Laravel](https://laravel.com/)
* [JavaScript](https://www.javascript.com/)
* [Bootstrap](https://getbootstrap.com/)
* [jQuery](https://jquery.com/)


<p align="right">(<a href="#top">back to top</a>)</p>

### Libraries

* [composer](https://getcomposer.org/)
* [illuminate](https://packagist.org/packages/illuminate/database)
* [Admin LTE](https://adminlte.io/)
* [Laravel Datatables](https://github.com/yajra/laravel-datatables)
* [Datatables](https://datatables.net/)
* [Spatie](https://github.com/spatie/laravel-permission)
* [Cybercog](https://github.com/cybercog/laravel-ban)
* [Stripe](https://stripe.com/docs/payments)
* [Sanctum](https://laravel.com/docs/master/sanctum)


<p align="right">(<a href="#top">back to top</a>)</p>

## Authors

* LinkedIn - [Nagwa Talaat](https://www.linkedin.com/in/nagwatalaat/)
* LinkedIn   - [Hossam Adel](https://www.linkedin.com/in/hossamadel23895/)
* LinkedIn   - [Sarah Abdeldaym](https://www.linkedin.com/in/sarah-abd-eldaym-594368183/)
* LinkedIn - [Monica Ashraf](https://www.linkedin.com/in/monica-ashraf-1b035816a/)
* LinkedIn - [Amira Emad](https://www.linkedin.com/in/amira-emad-161989213/)
* LinkedIn - [Mahitab Mohsen](https://www.linkedin.com/in/mahitab-mohsen-5446401bb/)
* LinkedIn   - [Shrouk Mamdoh](https://www.linkedin.com/in/shrouk-mamdoh-36510720a/)


<p align="right">(<a href="#top">back to top</a>)</p>
