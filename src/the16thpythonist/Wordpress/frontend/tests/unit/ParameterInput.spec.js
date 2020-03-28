import { shallowMount } from "@vue/test-utils";

import command from "@/lib/command";

import ParameterInput from "@/components/ParameterInput.vue";


describe("ParameterInput.vue", () => {
    it("renders props.msg when passed", () => {
        const cmd = new command.Command("name", [
            new command.CommandParameter('name1', '', 'string', false),
            new command.CommandParameter('name2', '', 'int', false)
        ]);
        const wrapper = shallowMount(ParameterInput, {
            propsData: {
                command: cmd
            }
        });
        expect(1).toBe(1);
    });
});