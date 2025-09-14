import { Collapsible } from './collapsible';
import { initAllComponentInstances } from '../../../Client';

(() => {
	initAllComponentInstances('collapsible', data => {
		const collapsible = new Collapsible(data);
		collapsible.init();
		return collapsible;
	});
})();
