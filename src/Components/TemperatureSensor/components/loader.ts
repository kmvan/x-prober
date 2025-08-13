import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { TemperatureSensorConstants } from './constants.ts';
import { TemperatureSensor as content } from './index.tsx';
import { TemperatureSensorNav as nav } from './nav.tsx';export const TemperatureSensorLoader: ModuleProps = {
  id: TemperatureSensorConstants.id,
  content,
  nav,
};
