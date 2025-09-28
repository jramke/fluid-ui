import { initAllComponentInstances } from 'fluid-primitives/client';
import { Dialog } from 'fluid-primitives/primitives/dialog';

(() => {
	initAllComponentInstances('dialog', ({ props }) => {
		const dialog = new Dialog(props);
		dialog.init();
		return dialog;
	});
})();
