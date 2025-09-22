import { Collapsible } from 'fluid-ui/primitives/collapsible';
import { getHydrationData, initAllComponentInstances } from 'fluid-ui/client';

(() => {
	initAllComponentInstances('collapsible', ({ props }) => {
		const collapsible = new Collapsible(props);
		collapsible.init();
		return collapsible;
	});
})();
