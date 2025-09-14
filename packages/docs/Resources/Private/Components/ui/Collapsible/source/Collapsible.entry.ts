import { Collapsible } from 'fluid-ui/primitives/collapsible';
import { initAllComponentInstances } from 'fluid-ui/client';

(() => {
	initAllComponentInstances('collapsible', data => {
		const collapsible = new Collapsible(data);
		collapsible.init();
		return collapsible;
	});
})();
