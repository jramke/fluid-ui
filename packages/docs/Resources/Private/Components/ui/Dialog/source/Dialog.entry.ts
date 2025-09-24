import { Dialog } from 'fluid-ui/primitives/dialog';
import { getHydrationData, initAllComponentInstances } from 'fluid-ui/client';

(() => {
	initAllComponentInstances('dialog', ({ props }) => {
		const dialog = new Dialog(props);
		dialog.init();
		return dialog;
	});
})();
