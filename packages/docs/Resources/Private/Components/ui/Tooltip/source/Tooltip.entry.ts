import { Tooltip } from 'fluid-ui/primitives/tooltip';
import { initAllComponentInstances } from 'fluid-ui/client';

(() => {
	initAllComponentInstances('tooltip', ({ props }) => {
		const tooltip = new Tooltip(props);
		tooltip.init();
		return tooltip;
	});
})();
