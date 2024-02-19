# Developer

## Installation

[Install Composer](https://getcomposer.org/doc/00-intro.md), PHPs package manager, if not available.

[Install NPM](https://www.npmjs.com/get-npm), Nodes package manager, if not available.


Clone the repository

```
git clone https://github.com/ckrack/ispindel-public-server
```

Install the node dependencies
```
npm install
```

Install PHP dependencies

```
composer install
```

Copy `example.env` to `.env` and modify at least the database settings.

```
copy ./src/.example.env ./src/.env
```

Run `gulp` to start the built-in PHP server and open a browser window.


## API

There is an API to write and read data from the Hydrometers.
For writing, a valid api-token is required and the post contains the Hydrometer-id.
For reading, a user either has to be logged in (for ui-updates) or use their api-token for remote applications.


## Example JSON from iSpindle

{"name":"eSpindel","ID":"123456","angle":71.11,"temperature":18.25,"battery":5.54,"gravity":24.89, "token": "abcdef1234567"
}
