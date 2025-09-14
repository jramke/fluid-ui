import { createNormalizer } from "@zag-js/types";
import { nanoid } from "nanoid";
import { INIT_STATE, MachineStatus, createScope, mergeProps } from "@zag-js/core";
import { proxy, subscribe } from "@zag-js/store";
import { compact, identity, isEqual, isFunction, isString, runIfFn, toArray, warn } from "@zag-js/utils";

//#region Resources/Private/Client/src/lib/normalize-props.ts
const propMap = {
	onFocus: "onFocusin",
	onBlur: "onFocusout",
	onChange: "onInput",
	onDoubleClick: "onDblclick",
	htmlFor: "for",
	className: "class",
	defaultValue: "value",
	defaultChecked: "checked"
};
const toStyleString = (style) => {
	let string = "";
	for (let key in style) {
		const value = style[key];
		if (value === null || value === void 0) continue;
		if (!key.startsWith("--")) key = key.replace(/[A-Z]/g, (match) => `-${match.toLowerCase()}`);
		string += `${key}:${value};`;
	}
	return string;
};
const normalizeProps = createNormalizer((props) => {
	return Object.entries(props).reduce((acc, [key, value]) => {
		if (value === void 0) return acc;
		if (key in propMap) key = propMap[key];
		if (key === "style" && typeof value === "object") {
			acc.style = toStyleString(value);
			return acc;
		}
		acc[key.toLowerCase()] = value;
		return acc;
	}, {});
});

//#endregion
//#region Resources/Private/Client/src/lib/spread-props.ts
const prevAttrsMap = new Map();
function spreadProps(node, attrs, machineId) {
	if (!node.__spreadId) node.__spreadId = `spread_${nanoid()}`;
	let machineElementKey = "";
	if (!machineId) machineElementKey = `${node.__spreadId}`;
	else machineElementKey = `${node.__spreadId}_${machineId}`;
	const oldAttrs = prevAttrsMap.get(machineElementKey) || {};
	const attrKeys = Object.keys(attrs);
	const addEvt = (e, f) => {
		node.addEventListener(e.toLowerCase(), f);
	};
	const remEvt = (e, f) => {
		node.removeEventListener(e.toLowerCase(), f);
	};
	const onEvents = (attr) => attr.startsWith("on");
	const others = (attr) => !attr.startsWith("on");
	const setup = (attr) => addEvt(attr.substring(2), attrs[attr]);
	const teardown = (attr) => remEvt(attr.substring(2), attrs[attr]);
	const apply = (attrName) => {
		let value = attrs[attrName];
		const oldValue = oldAttrs[attrName];
		if (value === oldValue) return;
		if (typeof value === "boolean") {
			if (!attrName.includes("aria-")) value = value || void 0;
		}
		if (value != null) {
			if ([
				"value",
				"checked",
				"htmlFor"
			].includes(attrName)) node[attrName] = value;
			else node.setAttribute(attrName.toLowerCase(), value);
			return;
		}
		node.removeAttribute(attrName.toLowerCase());
	};
	for (const key in oldAttrs) if (attrs[key] == null) node.removeAttribute(key.toLowerCase());
	const oldEvents = Object.keys(oldAttrs).filter(onEvents);
	oldEvents.forEach((evt) => {
		remEvt(evt.substring(2), oldAttrs[evt]);
	});
	attrKeys.filter(onEvents).forEach(setup);
	attrKeys.filter(others).forEach(apply);
	prevAttrsMap.set(machineElementKey, attrs);
	return function cleanup() {
		attrKeys.filter(onEvents).forEach(teardown);
	};
}

//#endregion
//#region Resources/Private/Client/src/lib/bindable.ts
function bindable(props) {
	const initial = props().value ?? props().defaultValue;
	if (props().debug) console.log(`[bindable > ${props().debug}] initial`, initial);
	const eq = props().isEqual ?? Object.is;
	const store = proxy({ value: initial });
	const controlled = () => props().value !== void 0;
	return {
		initial,
		ref: store,
		get() {
			return controlled() ? props().value : store.value;
		},
		set(nextValue) {
			const prev = store.value;
			const next = isFunction(nextValue) ? nextValue(prev) : nextValue;
			if (props().debug) console.log(`[bindable > ${props().debug}] setValue`, {
				next,
				prev
			});
			if (!controlled()) store.value = next;
			if (!eq(next, prev)) props().onChange?.(next, prev);
		},
		invoke(nextValue, prevValue) {
			props().onChange?.(nextValue, prevValue);
		},
		hash(value) {
			return props().hash?.(value) ?? String(value);
		}
	};
}
bindable.cleanup = (_fn) => {};
bindable.ref = (defaultValue) => {
	let value = defaultValue;
	return {
		get: () => value,
		set: (next) => {
			value = next;
		}
	};
};

//#endregion
//#region Resources/Private/Client/src/lib/refs.ts
function createRefs(refs) {
	const ref = { current: refs };
	return {
		get(key) {
			return ref.current[key];
		},
		set(key, value) {
			ref.current[key] = value;
		}
	};
}

//#endregion
//#region Resources/Private/Client/src/lib/machine.ts
var Machine$1 = class {
	scope;
	ctx;
	prop;
	state;
	refs;
	computed;
	event = { type: "" };
	previousEvent;
	effects = new Map();
	transition = null;
	cleanups = [];
	subscriptions = [];
	getEvent = () => ({
		...this.event,
		current: () => this.event,
		previous: () => this.previousEvent
	});
	getState = () => ({
		...this.state,
		matches: (...values) => values.includes(this.state.get()),
		hasTag: (tag) => !!this.machine.states[this.state.get()]?.tags?.includes(tag)
	});
	debug = (...args) => {
		if (this.machine.debug) console.log(...args);
	};
	notify = () => {
		this.publish();
	};
	constructor(machine, userProps = {}) {
		this.machine = machine;
		const { id, ids, getRootNode } = runIfFn(userProps);
		this.scope = createScope({
			id,
			ids,
			getRootNode
		});
		const prop = (key) => {
			const __props = runIfFn(userProps);
			const props = machine.props?.({
				props: compact(__props),
				scope: this.scope
			}) ?? __props;
			return props[key];
		};
		this.prop = prop;
		const context = machine.context?.({
			prop,
			bindable,
			scope: this.scope,
			flush(fn) {
				queueMicrotask(fn);
			},
			getContext() {
				return ctx;
			},
			getComputed() {
				return computed;
			},
			getRefs() {
				return refs;
			},
			getEvent: this.getEvent.bind(this)
		});
		if (context) Object.values(context).forEach((item) => {
			const unsub = subscribe(item.ref, () => this.notify());
			this.cleanups.push(unsub);
		});
		const ctx = {
			get(key) {
				return context?.[key].get();
			},
			set(key, value) {
				context?.[key].set(value);
			},
			initial(key) {
				return context?.[key].initial;
			},
			hash(key) {
				const current = context?.[key].get();
				return context?.[key].hash(current);
			}
		};
		this.ctx = ctx;
		const computed = (key) => {
			return machine.computed?.[key]({
				context: ctx,
				event: this.getEvent(),
				prop,
				refs: this.refs,
				scope: this.scope,
				computed
			}) ?? {};
		};
		this.computed = computed;
		const refs = createRefs(machine.refs?.({
			prop,
			context: ctx
		}) ?? {});
		this.refs = refs;
		const state = bindable(() => ({
			defaultValue: machine.initialState({ prop }),
			onChange: (nextState, prevState) => {
				if (prevState) {
					const exitEffects = this.effects.get(prevState);
					exitEffects?.();
					this.effects.delete(prevState);
				}
				if (prevState) this.action(machine.states[prevState]?.exit);
				this.action(this.transition?.actions);
				const cleanup = this.effect(machine.states[nextState]?.effects);
				if (cleanup) this.effects.set(nextState, cleanup);
				if (prevState === INIT_STATE) {
					this.action(machine.entry);
					const cleanup$1 = this.effect(machine.effects);
					if (cleanup$1) this.effects.set(INIT_STATE, cleanup$1);
				}
				this.action(machine.states[nextState]?.entry);
			}
		}));
		this.state = state;
		this.cleanups.push(subscribe(this.state.ref, () => this.notify()));
	}
	send = (event) => {
		if (this.status !== MachineStatus.Started) return;
		queueMicrotask(() => {
			this.previousEvent = this.event;
			this.event = event;
			this.debug("send", event);
			let currentState = this.state.get();
			const transitions = this.machine.states[currentState].on?.[event.type] ?? this.machine.on?.[event.type];
			const transition = this.choose(transitions);
			if (!transition) return;
			this.transition = transition;
			const target = transition.target ?? currentState;
			this.debug("transition", transition);
			const changed = target !== currentState;
			if (changed) this.state.set(target);
			else this.action(transition.actions);
		});
	};
	action = (keys) => {
		const strs = isFunction(keys) ? keys(this.getParams()) : keys;
		if (!strs) return;
		const fns = strs.map((s) => {
			const fn = this.machine.implementations?.actions?.[s];
			if (!fn) warn(`[zag-js] No implementation found for action "${JSON.stringify(s)}"`);
			return fn;
		});
		for (const fn of fns) fn?.(this.getParams());
	};
	guard = (str) => {
		if (isFunction(str)) return str(this.getParams());
		return this.machine.implementations?.guards?.[str](this.getParams());
	};
	effect = (keys) => {
		const strs = isFunction(keys) ? keys(this.getParams()) : keys;
		if (!strs) return;
		const fns = strs.map((s) => {
			const fn = this.machine.implementations?.effects?.[s];
			if (!fn) warn(`[zag-js] No implementation found for effect "${JSON.stringify(s)}"`);
			return fn;
		});
		const cleanups = [];
		for (const fn of fns) {
			const cleanup = fn?.(this.getParams());
			if (cleanup) cleanups.push(cleanup);
		}
		return () => cleanups.forEach((fn) => fn?.());
	};
	choose = (transitions) => {
		return toArray(transitions).find((t) => {
			let result = !t.guard;
			if (isString(t.guard)) result = !!this.guard(t.guard);
			else if (isFunction(t.guard)) result = t.guard(this.getParams());
			return result;
		});
	};
	start() {
		this.status = MachineStatus.Started;
		this.debug("initializing...");
		this.state.invoke(this.state.initial, INIT_STATE);
		this.setupTrackers();
	}
	stop() {
		this.effects.forEach((fn) => fn?.());
		this.effects.clear();
		this.transition = null;
		this.action(this.machine.exit);
		this.cleanups.forEach((unsub) => unsub());
		this.cleanups = [];
		this.status = MachineStatus.Stopped;
		this.debug("unmounting...");
	}
	subscribe = (fn) => {
		this.subscriptions.push(fn);
	};
	status = MachineStatus.NotStarted;
	get service() {
		return {
			state: this.getState(),
			send: this.send,
			context: this.ctx,
			prop: this.prop,
			scope: this.scope,
			refs: this.refs,
			computed: this.computed,
			event: this.getEvent(),
			getStatus: () => this.status
		};
	}
	publish = () => {
		this.callTrackers();
		this.subscriptions.forEach((fn) => fn(this.service));
	};
	trackers = [];
	setupTrackers = () => {
		this.machine.watch?.(this.getParams());
	};
	callTrackers = () => {
		this.trackers.forEach(({ deps, fn }) => {
			const next = deps.map((dep) => dep());
			if (!isEqual(fn.prev, next)) {
				fn();
				fn.prev = next;
			}
		});
	};
	getParams = () => ({
		state: this.getState(),
		context: this.ctx,
		event: this.getEvent(),
		prop: this.prop,
		send: this.send,
		action: this.action,
		guard: this.guard,
		track: (deps, fn) => {
			fn.prev = deps.map((dep) => dep());
			this.trackers.push({
				deps,
				fn
			});
		},
		refs: this.refs,
		computed: this.computed,
		flush: identity,
		scope: this.scope,
		choose: this.choose
	});
};

//#endregion
//#region Resources/Private/Client/src/lib/component.ts
var Component = class {
	document;
	machine;
	api;
	hydrator = null;
	userProps;
	get doc() {
		return this.document;
	}
	constructor(props, userDocument = document) {
		this.document = userDocument;
		this.userProps = props;
		this.machine = this.initMachine(props);
		this.api = this.initApi();
	}
	init() {
		this.hydrator = new ComponentHydrator(this.name, this.machine.scope.id, this.machine.scope.ids, this.doc);
		this.render();
		this.machine.subscribe(() => {
			this.api = this.initApi();
			this.render();
		});
		this.machine.start();
	}
	updateProps(props) {
		this.machine.stop();
		this.machine = this.initMachine({
			...this.userProps,
			...props
		});
		this.api = this.initApi();
		this.init();
	}
	destroy = () => {
		this.machine.stop();
		this.hydrator?.destroy();
	};
	spreadProps(node, attrs) {
		spreadProps(node, attrs, this.machine.scope.id);
	}
	getElement(part) {
		return this.hydrator?.getElement(part) || null;
	}
	getElements(part) {
		return this.hydrator?.getElements(part) || [];
	}
	portalElement(el, target = this.doc.body) {
		if (!el) return;
		if (el.parentNode !== target) {
			target.appendChild(el);
			el.setAttribute("data-portalled", "true");
		}
	}
};

//#endregion
//#region Resources/Private/Client/src/lib/hydration.ts
function getHydrationData(component, id) {
	const hydrationData = window.FluidUI.hydrationData;
	if (!hydrationData || typeof hydrationData !== "object") return null;
	if (!component) return hydrationData;
	if (!hydrationData[component]) return null;
	if (!id) return hydrationData[component];
	return hydrationData[component][id] || null;
}
function initAllComponentInstances(componentName, callback) {
	const hydrationInstances = getHydrationData(componentName);
	if (!hydrationInstances) return;
	Object.keys(hydrationInstances).forEach((id) => {
		if (hydrationInstances[id].__controlled) return;
		const instance = callback(hydrationInstances[id]);
		console.log({
			componentName,
			id,
			instance
		});
	});
}
var ComponentHydrator = class {
	componentName;
	doc;
	rootId;
	ids;
	elementRefs = new Map();
	constructor(componentName, rootId, ids = {}, doc = document) {
		this.componentName = componentName;
		this.doc = doc;
		if (!rootId) throw new Error(`Root ID is required for component hydration: ${componentName}`);
		this.rootId = rootId;
		this.ids = ids;
	}
	getElement(part) {
		if (this.elementRefs.has(part)) return this.elementRefs.get(part) || null;
		let element = null;
		if (this.ids[part]) element = this.doc.getElementById(this.ids[part]);
		else element = this.doc.querySelector(`[data-hydrate-${this.componentName}="${this.rootId}"][data-part="${part}"][data-scope="${this.componentName}"]`);
		if (element) {
			this.elementRefs.set(part, element);
			element.removeAttribute(`data-hydrate-${this.componentName}`);
			element.__rootId = this.rootId;
		}
		return element;
	}
	getElements(part) {
		if (this.elementRefs.has(part)) return this.elementRefs.get(part);
		let elements = [];
		if (this.ids[part]) elements = Array.from(this.doc.querySelectorAll(`#${this.ids[part]}`));
		else elements = Array.from(this.doc.querySelectorAll(`[data-hydrate-${this.componentName}="${this.rootId}"][data-part="${part}"][data-scope="${this.componentName}"]`));
		this.elementRefs.set(part, elements);
		elements.forEach((el) => el.removeAttribute(`data-hydrate-${this.componentName}`));
		return elements;
	}
	generateRefAttributesString(part) {
		const id = this.ids[part] || `${this.rootId}-${part}`;
		return `data-scope="${this.componentName}" data-part="${part}" data-hydrate-${this.componentName}="${id}"`;
	}
	setRefAttributes(element, part) {
		const attributes = this.generateRefAttributesString(part);
		const attributesArray = attributes.split(" ").map((attr) => attr.trim());
		attributesArray.forEach((attr) => {
			const [key, value] = attr.split("=");
			element.setAttribute(key, value.replace(/"/g, ""));
		});
	}
	destroy() {
		this.elementRefs.clear();
	}
};

//#endregion
export { Component, ComponentHydrator, Machine$1 as Machine, getHydrationData, initAllComponentInstances, mergeProps, normalizeProps, spreadProps };