import { createMachine, type EventObject, type Service } from '@zag-js/core';
import type { NormalizeProps, PropTypes } from '@zag-js/types';
import { Component, initAllComponentInstances, Machine, normalizeProps } from 'fluid-primitives';

interface Props {}

interface Schema {
	context: { value: number };
	state: 'idle' | 'ticking';
	event: EventObject;
	action: string;
	effect: string;
}

interface Api {
	value: number;
	getIncProps(): PropTypes['element'];
	getDecProps(): PropTypes['element'];
	getValueProps(): PropTypes['element'];
	getStartProps(): PropTypes['element'];
	getStopProps(): PropTypes['element'];
}

const machine = createMachine<Schema>({
	context({ bindable }) {
		return { value: bindable(() => ({ defaultValue: 0 })) };
	},

	initialState() {
		return 'idle';
	},

	states: {
		idle: {
			on: {
				INC: {
					actions: ['increment'],
				},
				DEC: {
					actions: ['decrement'],
				},
				START: {
					target: 'ticking',
					actions: ['increment'],
				},
			},
		},
		ticking: {
			effects: ['keepTicking'],
			on: {
				TICK: {
					actions: ['increment'],
				},
				STOP: {
					target: 'idle',
				},
			},
		},
	},

	watch({ track, context }) {
		track([() => context.get('value')], () => {
			console.log('value changed', context.get('value'));
		});
	},

	implementations: {
		actions: {
			increment({ context }) {
				context.set('value', context.get('value') + 1);
			},
			decrement({ context }) {
				context.set('value', context.get('value') - 1);
			},
		},
		effects: {
			keepTicking({ context }) {
				const id = setInterval(() => {
					context.set('value', context.get('value') + 1);
				}, 1000);
				return () => clearInterval(id);
			},
		},
	},
});

function connect<T extends PropTypes>(service: Service<Schema>, normalize: NormalizeProps<T>): Api {
	const { context, send, state } = service;
	return {
		value: context.get('value'),
		getIncProps() {
			return normalize.button({
				type: 'button',
				disabled: state.matches('ticking'),
				onClick: () => send({ type: 'INC' }),
			});
		},
		getDecProps() {
			return normalize.button({
				type: 'button',
				disabled: state.matches('ticking'),
				onClick: () => send({ type: 'DEC' }),
			});
		},
		getValueProps() {
			return normalize.input({
				children: context.get('value').toString(),
			});
		},
		getStartProps() {
			return normalize.button({
				type: 'button',
				onClick: () => send({ type: 'START' }),
				hidden: state.matches('ticking'),
			});
		},
		getStopProps() {
			return normalize.button({
				type: 'button',
				onClick: () => send({ type: 'STOP' }),
				hidden: !state.matches('ticking'),
			});
		},
	};
}

class Counter extends Component<Props, Api> {
	name = 'counter';

	initMachine(props: Props) {
		return new Machine(machine, props);
	}

	initApi() {
		return connect(this.machine.service, normalizeProps);
	}

	render() {
		const valueEl = this.getElement('value');
		if (valueEl) this.spreadProps(valueEl, this.api.getValueProps());

		const incEl = this.getElement('inc');
		if (incEl) this.spreadProps(incEl, this.api.getIncProps());

		const decEl = this.getElement('dec');
		if (decEl) this.spreadProps(decEl, this.api.getDecProps());

		const startEl = this.getElement('start');
		if (startEl) this.spreadProps(startEl, this.api.getStartProps());

		const stopEl = this.getElement('stop');
		if (stopEl) this.spreadProps(stopEl, this.api.getStopProps());
	}
}

(() => {
	initAllComponentInstances('counter', ({ props }) => {
		const counter = new Counter(props);
		counter.init();
		return counter;
	});
})();
