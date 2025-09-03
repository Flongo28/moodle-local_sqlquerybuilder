# SQL Query Builder #
Enables other plugins to build queries with a builder class.

This plugin is a library used for other plugins. It allows developers
to create queries in a flexible, safe and easy way. 

> ⚠️ **SQL Injections**. <br>
> This plugin currently does not perform parameter binding or input validation.  
**Do not use untrusted user input** in queries. It is planned to fix this in future versions.

> Work in Progress!

## Usage
The idea is to use the db class to create a query, as demonstrated in this simple example:
```
$paul = db::table('user')->where('firstname', '=', 'Paul')
                         ->get();
```

## Advantages
- compatible
  - simplifies writing of cross-database queries
- readable
  - get rid of SQL in PHP
- productive
  - build queries faster
- flexible
  - adapt similar queries for different use-cases

## TODOS
- Collect parameters to sanitize them
- Provide LIMIT and OFFSET
- Allow parameters in select
- Provide more raw methods to make the plugin more flexible
- Update functionality
- Delete functionality
- Create functionality

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/sqlquerybuilder

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##
This plugin was developed at the Moodle Moot Dach 2025. It is the result of a collaboration between:
- 2025 Dennis Phahl
- 2025 Matthias Opitz
- 2025 Daniel Meißner
- 2025 Mahmoud Chehada
- 2025 Konrad Ebel <konrad.ebel@oncampus.de>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
