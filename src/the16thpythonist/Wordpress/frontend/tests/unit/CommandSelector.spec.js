import { shallowMount } from "@vue/test-utils";

import command from "@/lib/command";

import CommandSelector from "@/components/CommandSelector.vue";


describe("CommandSelector.vue", () => {
    it("renders props.msg when passed", () => {
        const commands = [
            new command.Command('test1', []),
            new command.Command('test2', [])
        ];
        const wrapper = shallowMount(CommandSelector, {
            propsData: {
                commands: commands
            }
        });
        expect(wrapper.text()).toMatch("hello");
    });
});