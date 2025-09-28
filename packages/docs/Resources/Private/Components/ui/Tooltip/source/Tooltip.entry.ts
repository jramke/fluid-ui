import { initAllComponentInstances } from 'fluid-ui/client';
import { Tooltip } from 'fluid-ui/primitives/tooltip';

(() => {
	initAllComponentInstances('tooltip', ({ props }) => {
		const tooltip = new Tooltip(props);
		tooltip.init();
		return tooltip;
	});
})();
