import type { Component } from './lib/component';
import type { ComponentHydrator, Machine } from './lib';

declare global {
	interface Window {
		FluidUI: {
			hydrationData: {
				[componentName: string]: {
					[id: string]: ComponentHydrationData;
				};
			};
			uncontrolledComponents: {
				[componentName: string]: {
					[id: string]: Component<unknown, unknown>;
				};
			};
		};
	}
}

export interface ComponentInterface<Api> {
	document: Document;
	machine: Machine<any>;
	api: Api;
	hydrator: ComponentHydrator | null;

	init(): void;
	destroy(): void;
	render(): void;
}

export interface ComponentHydrationData {
	id: string;
	__controlled: boolean;
	[key: string]: any;
}
