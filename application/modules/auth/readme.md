# auth module
This module contains every elements needed for user authentication.

## Configuration
This section describes the module's configurations available in the config directory.

### Access levels
Define the different access levels needed for your application.
By default, 3 levels are defined : admin, registered and guest.

### Validation rules
Define the validation rules for the login form.

## Public functions
This section describes the public functions that can be called from another module.

### login
Display a login form, check login information and create session variables.

### logout
Reset session and redirect to the homepage.

## Database and models
This section describes the database tables needed for this module, the corresponding models and eventual particularities.

### user
The user table and model contain basic user authentication information : username and password.
It uses soft delete feature, provided by the base model in application's core directory. Archive field is the soft delete key.

### user_type
The user_type table and model define the users access level with an access level name and an integer access level number copied in session variable.
This access level number, combined with the access levels defined in configuration file, is used to decide what part of the application the user is allowed to access.

## Dependencies
No dependencies for this module other than the libraries in "Built with" section.

## Built With
* [CodeIgniter](https://www.codeigniter.com/) - PHP framework
* [CodeIgniter modular extensions HMVC](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc) - HMVC for CodeIgniter
* [CodeIgniter base model](https://github.com/jamierumbelow/codeigniter-base-model) - Generic model
* [Bootstrap](https://getbootstrap.com/) - To simplify views design

## Authors
* **Orif, domaine informatique** - *Creating and following this module* - [GitHub account](https://github.com/OrifInformatique)
