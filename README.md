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