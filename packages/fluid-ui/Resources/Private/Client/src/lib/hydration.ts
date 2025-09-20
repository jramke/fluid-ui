import { Component } from './component';
import type { ComponentHydrationData } from '../types';

export function getHydrationData(component: string): Record<string, ComponentHydrationData> | null;
export function getHydrationData(component: string, id: string): ComponentHydrationData | null;
export function getHydrationData(component?: string, id?: string) {
	const hydrationData = window.FluidUI.hydrationData;

	if (!hydrationData || typeof hydrationData !== 'object') {
		return null;
	}

	if (!component) {
		return hydrationData;
	}

	if (!hydrationData[component]) {
		return null;
	}

	if (!id) {
		return hydrationData[component];
	}

	return hydrationData[component][id] || null;
}

export function initAllComponentInstances(
	componentName: string,
	callback: (data: ComponentHydrationData) => Component<unknown, unknown>
) {
	const hydrationInstances = getHydrationData(componentName);
	if (!hydrationInstances) return;

	Object.keys(hydrationInstances).forEach(id => {
		if (hydrationInstances[id].controlled) return;
		const instance = callback(hydrationInstances[id]);

		if (!window.FluidUI.uncontrolledInstances[componentName]) {
			window.FluidUI.uncontrolledInstances[componentName] = {};
		}
		window.FluidUI.uncontrolledInstances[componentName][id] = instance;
	});
}

export class ComponentHydrator {
	componentName: string;
	doc: Document;
	rootId: string;
	ids: { [key: string]: string };
	elementRefs = new Map<string, HTMLElement | HTMLElement[]>();

	constructor(
		componentName: string,
		rootId: string | undefined,
		ids: { [key: string]: string } = {},
		doc: Document = document
	) {
		this.componentName = componentName;
		this.doc = doc;
		if (!rootId) {
			throw new Error(`Root ID is required for component hydration: ${componentName}`);
		}
		this.rootId = rootId;
		this.ids = ids;
	}

	getElement<T extends HTMLElement>(part: string): T | null {
		if (this.elementRefs.has(part)) {
			return (this.elementRefs.get(part) as T) || null;
		}

		let element: T | null = null;

		if (this.ids[part]) {
			element = this.doc.getElementById(this.ids[part]) as T | null;
		} else {
			element = this.doc.querySelector<T>(
				`[data-hydrate-${this.componentName}="${this.rootId}"][data-part="${part}"][data-scope="${this.componentName}"]`
			);
		}

		if (element) {
			this.elementRefs.set(part, element);
			element.removeAttribute(`data-hydrate-${this.componentName}`);
			(element as any).__rootId = this.rootId; // Store rootId for reference)
		}

		return element;
	}

	getElements<T extends HTMLElement>(part: string): T[] {
		if (this.elementRefs.has(part)) {
			return this.elementRefs.get(part) as T[];
		}

		let elements: T[] = [];

		if (this.ids[part]) {
			elements = Array.from(this.doc.querySelectorAll<T>(`#${this.ids[part]}`));
		} else {
			elements = Array.from(
				this.doc.querySelectorAll<T>(
					`[data-hydrate-${this.componentName}="${this.rootId}"][data-part="${part}"][data-scope="${this.componentName}"]`
				)
			);
		}

		this.elementRefs.set(part, elements);
		elements.forEach(el => el.removeAttribute(`data-hydrate-${this.componentName}`));

		return elements;
	}

	generateRefAttributesString(part: string): string {
		const id = this.ids[part] || `${this.rootId}-${part}`;
		return `data-scope="${this.componentName}" data-part="${part}" data-hydrate-${this.componentName}="${id}"`;
	}

	setRefAttributes(element: HTMLElement, part: string): void {
		const attributes = this.generateRefAttributesString(part);
		const attributesArray = attributes.split(' ').map(attr => attr.trim());
		attributesArray.forEach(attr => {
			const [key, value] = attr.split('=');
			element.setAttribute(key, value.replace(/"/g, ''));
		});
	}

	destroy() {
		this.elementRefs.clear();
	}
}
