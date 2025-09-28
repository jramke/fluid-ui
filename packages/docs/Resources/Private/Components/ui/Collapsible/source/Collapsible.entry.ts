import { initAllComponentInstances } from 'fluid-ui/client';
import { Collapsible } from 'fluid-ui/primitives/collapsible';

(() => {
	initAllComponentInstances('collapsible', ({ props }) => {
		const collapsible = new Collapsible(props);
		collapsible.init();
		return collapsible;
	});
})();
