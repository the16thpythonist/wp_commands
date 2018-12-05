# Wordpress Command Module


## Changelog

### 0.0.0.0 - 17.07.2018

- abstract class Command, that can be subclassed to create background commands
- class CommandReference; Stores a list of all the registered commands
- class CommandMenu; Create a new admin page menu, that can be used to execute the tasks from the backend
- class CommandMenuRegistration; Registers the admin page menu with wordpress

Todo:
- Make the menu in the backend prettier
- Add parameter support in the backend menu

### 0.0.0.1 - 09.08.2018

- Fixed the bug with the command name being used as the title of the log files always being the last command that 
was registered in the code with the 'register' command.
    - Added static class 'CommandNamePocket', which stores the command names in reference to the class names of the 
    Command child classes
    
### 0.0.0.2 - 14.08.2018

- Fixed a bug with the command name not being registered in the CommandNamePocket and breaking the whole command 
functionality

### 0.0.0.3 - 15.08.2018

- Added the dashboard widget, which displays the links of the log posts for the most recently executed commands.
    - Added class 'Wordpress/CommandDashboardRegistration'

### 0.0.0.4 - 15.08.2018

- Added the static method 'fromCallable' to the Command class. Using this method with a command name and a callable 
function, a new command can be registered directly, which does nothing but execute the function during its runtime.

### 0.0.0.5 - 29.08.2018

- Fixed an issue with the dashboard plugin completely killing the CPU, because an index ran out of bounds, when there 
were less actual log posts, then the number of recent posts to be displayed

### 0.0.0.6 - 29.08.2018

- Added the functionality to choose and execute the commands directly from the admin dashboard.

### 0.0.0.7 - 17.10.2018

- Fixed a minor issue with the menu box not using the correct hook for the registration
- Included the package [wp-cpt-lib](https://github.com/the16thpythonist/wp-cpt-lib.git) to the composer requirements. 
It is mainly a base package to introduce custom post types in a very object oriented way.

### 0.0.0.8 - 06.11.2018

- Minor bug fixes

### 0.0.0.9 - 19.11.2018

- Added a facade, which does all the registration in wordpress
- Added JS scripts, that make it possible to input parameters to the executed command 
from the Widget in the Admin dashboard
- disabled the registration for the separate admin menu. Doing everything over the dashboard widget for now

### 0.0.0.10 - 05.12.2018

- Added a function to the facade, which will return all the LogPost objects which have been 
created by previously executed commands
- Changed the dashboard widget, which is used for command execution
    - If there are no parameters available for a command, an info message will be displayed,
    - Changed the design to be more modern and colorful
- Added a utility AJAX function, which will listen to the action 'get_recent_commands'. It 
will returns a list with info about the most recently executed commands
- Renamed the 'command.js' script to 'command-widget.js' as it only contains javascript specific 
to the functionality of the dashboard widget
- Added the 'command.js' script, which will be used for utility functions provided by this package
    - Added function 'getRecentCommands', which will send an Ajax request to receive a list 
    of info on the most recently executed commands on the website.
    - Changed the display of the most recently executed commands to update after the execute 
    Button has been pressed. The entries contain links, which will lead to the edit page of the log 
    post, that was created by the named command.