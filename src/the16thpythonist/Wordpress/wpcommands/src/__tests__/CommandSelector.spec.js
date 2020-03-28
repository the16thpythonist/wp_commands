import { mount } from '@vue/test-utils'
import CommandSelector from '../components/CommandSelector'

describe('CommandSelector', () => {
    // Now mount the component and you have the wrapper
    const wrapper = mount(CommandSelector);

    it('renders the correct markup', () => {
        expect(wrapper.html()).toContain('<span class="count">0</span>')
    });
});
