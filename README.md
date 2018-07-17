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