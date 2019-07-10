# Questionnaires management

The purpose of this application is to record questions about different topics and then being able to generate questionnaires with a random subset of these questions.
Different types of questions are available, such as free text answer questions, multiple choice questions, cloze texts and others.

This application is developed in french and not (yet ?) translated in other languages.
However, CodeIgniter's language files are used properly and it would be easy to make your own translation.

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Install a local PHP server, [XAMPP for example](https://www.apachefriends.org)

### Installing

1. Download [our latest release](https://github.com/OrifInformatique/gestion_questionnaires/releases)
2. Unzip your download in your project's directory (in your local PHP server)
3. Generate a local database, using the latest "gestion_questionnaires_structure.sql" file, witch you find in the "database" directory
4. Modify file application/config/config.php with your local site's URL and language (french by default)
```

[...]

$config['base_url'] = 'http://localhost/your_project_directory/';

[...]

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= 'french';

[...]

```

5. Modify file application/config/database.php with the informations of your local database
```

$db['default'] = array(
	[...]
	'hostname' => 'your_database_server',
	'username' => 'your_user',
	'password' => 'your_password',
	'database' => 'your_database_name',
	[...]
);

```

## Built With

* [CodeIgniter](https://www.codeigniter.com/) - PHP framework
* [CodeIgniter base model](https://github.com/jamierumbelow/codeigniter-base-model) - Generic model
* [Bootstrap](https://getbootstrap.com/) - To simplify views design
* [FPDF](http://www.fpdf.org/) - To generate pdf questionnaires
* [PHPExcel - DEPRECATED](https://github.com/PHPOffice/PHPExcel) - To import original questions sets from Excel files (not to use anymore)

## Authors

* **Orif, domaine informatique** - *Initiating and following the project* - [GitHub account](https://github.com/OrifInformatique)

See also the list of [contributors](https://github.com/OrifInformatique/gestion_questionnaires/contributors) who participated in this project.
