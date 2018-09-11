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
