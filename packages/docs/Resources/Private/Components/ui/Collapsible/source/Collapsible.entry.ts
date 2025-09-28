import { initAllComponentInstances } from 'fluid-primitives/client';
import { Collapsible } from 'fluid-primitives/primitives/collapsible';

(() => {
	initAllComponentInstances('collapsible', ({ props }) => {
		const collapsible = new Collapsible(props);
		collapsible.init();
		return collapsible;
	});
})();
