import { initAllComponentInstances } from 'fluid-primitives/client';
import { Clipboard } from 'fluid-primitives/primitives/clipboard';

(() => {
	initAllComponentInstances('clipboard', ({ props }) => {
		const clipboard = new Clipboard(props);
		clipboard.init();
		return clipboard;
	});
})();
