import { Tooltip } from 'fluid-ui/primitives/tooltip';
import { initAllComponentInstances } from 'fluid-ui/client';

(() => {
	initAllComponentInstances('tooltip', data => {
		const tooltip = new Tooltip(data);
		tooltip.init();
		return tooltip;
	});
})();
