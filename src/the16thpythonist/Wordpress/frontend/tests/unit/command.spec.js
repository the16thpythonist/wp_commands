import command from "@/lib/command";

// CLASS "Command"
describe("Command", () => {

    // If Command constructions works
   it("constructs correctly", () => {
       let cmd = new command.Command('test', []);

       expect(cmd).toHaveProperty('name');
       expect(cmd).toHaveProperty('parameters');

       expect(cmd.name).toBe('test');
       expect(cmd.parameters).toBeInstanceOf(Array);
   });

});

// CLASS "CommandParameter"
describe("CommandParameter", () => {

    // If CommandParameter construction works
    it("constructs correctly", () => {
       let param = new command.CommandParameter('test1', 'default', 'string', true);

       expect(param).toHaveProperty('name');
       expect(param).toHaveProperty('default');
       expect(param).toHaveProperty('type');
       expect(param).toHaveProperty('optional');

       expect(param.optional).toBe(true);
    });
});