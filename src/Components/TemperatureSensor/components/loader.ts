import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { TemperatureSensorConstants } from './constants.ts';
import { TemperatureSensor as component } from './index.tsx';
import { TemperatureSensorNav as nav } from './nav.tsx';

export const TemperatureSensorLoader = (): CardProps => {
  return {
    id: TemperatureSensorConstants.id,
    title: gettext('Temperature sensor'),
    priority: 100,
    component,
    nav,
  };
};
